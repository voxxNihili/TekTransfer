import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import { Button } from "@material-ui/core";
import SearchComponent from "../../Components/Utils/SearchComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import SearchDialog from "../../Components/Utils/Dialog";
import swal from "sweetalert";
import moment from "moment";
import { TextField } from "@mui/material";
import Tippy from "@tippyjs/react";
import useStyles from "../../Components/style/theme";
const ExcelList = (props, fixedHeader, fixedHeaderScrollHeight) => {
    const classes = useStyles();
    const [data, setData] = useState([]);
    const [count, setCount] = useState([]);
    const [refresh, setRefresh] = useState(false);
    const [open, setOpen] = useState(false);
    const [companies, setCompanies] = useState([]);
    const [transferStatus, setTransferStatus] = useState("");
    const [companyOf, setCompanyOf] = useState("");
    const [typeOf, setTypeOf] = useState("");
    const [beginDate, setBeginDate] = useState();
    const [endDate, setEndDate] = useState();

    const [filter, setFilter] = useState({
        filteredData: [],
        text: "",
        isFilter: false,
    });

    const transferStatusData = [
        { label: "Aktarım Durumu Seçiniz", value: "0", key: "999" },
        { label: "Başarılı", value: "200", key: "0" },
        { label: "Başarısız", value: "201", key: "1" },
    ];
    const typeOfData = [
        { label: "Tür Seçiniz", value: "0", key: "999" },
        { label: "Alış", value: "1", key: "0" },
        { label: "Satış", value: "2", key: "1" },
    ];

    useEffect(() => {
        axios
            .get(`/api/invoiceExcel`, {
                headers: {
                    Authorization: "Bearer " + props?.accessToken,
                },
            })
            .then((res) => {
                setData(res.data.data);
                setCount(res.data.count);
            })
            .catch((e) => console.log(e));
        axios
            .get(`/api/logoCompanyList`, {
                headers: {
                    Authorization: "Bearer " + props?.accessToken,
                },
            })
            .then((res) => {
                let newList = res.data.data.map((item) => {
                    return {
                        label: item?.name,
                        key: item?.id,
                        value: item?.company_id,
                    };
                });
                newList.unshift({
                    label: "Şirket Seçiniz",
                    value: 0,
                    key: "9999",
                });
                console.log("newlist", newList);
                setCompanies(newList);

                console.log("res", res);
            })
            .catch((e) => console.log(e));
    }, [refresh]);

    const filterItem = (e) => {
        const filterText = e.target.value;
        if (filterText != "") {
            const filteredItems = data.filter(
                (item) =>
                    item.customer_name &&
                    item.customer_name
                        .toLowerCase()
                        .includes(filterText.toLowerCase())
            );

            setFilter({
                filteredData: filteredItems,
                text: filterText,
                isFilter: true,
            });
        } else {
            setFilter({
                filteredData: [],
                text: "",
                isFilter: false,
            });
        }
    };

    const handleSubmit = () => {
        var params = {
            beginDate: beginDate && beginDate,
            endDate: endDate && endDate,
            transferStatus: transferStatus ? transferStatus : null,
            company_id: companyOf ? companyOf : null,
            typeOf: typeOf ? typeOf : null,
        };
        axios
            .get(`/api/invoice`, {
                params,
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                setData(res.data.data);
                setCount(res.data.count);
            })
            .catch((e) => console.log(e));
        console.log("ahahahaha");
        setOpen(false);
    };
    const handleBeginDateChange = (e) => {
        setBeginDate(e);
    };
    const handleEndDateChange = (e) => {
        setEndDate(e);
    };
    const handleClickOpen = () => {
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
    };
    const deleteItem = (item) => {
        swal({
            title: "Silmek istediğine emin misin ?",
            text: "Silinince veriler geri gelmeyecektir",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {});
    };

    const conditionalRowStyles = [
        { when: (row) => row.status == 201, style: { color: "red" } },
        { when: (row) => row.status == 200, style: { color: "green" } },
    ];

    return (
        <div className="row">
            <div className="col-md-12" style={{ backgroundColor: "white" }}>
                <div className="row mx-2 my-1">
                    <div className="col-md-2">
                        <div className="card-item">
                            <span>
                                Başarılı Excel Sayısı: {count?.successInvoice}
                            </span>
                        </div>
                    </div>
                    <div className="col-md-3">
                        <div className="card-item">
                            <span>
                                Beklemede Excel Sayısı: {count?.failedInvoice}
                            </span>
                        </div>
                    </div>
                    <div className="col-md-3">
                        <div className="card-item">
                            <span>
                                Başarısız Excel Sayısı: {count?.failedInvoice}
                            </span>
                        </div>
                    </div>
                    <div className="col-md-2">
                        <div className="card-item">
                            <span>
                                Toplam Excel Sayısı: {count?.totalInvoice}
                            </span>
                        </div>
                    </div>
                    <div className="col-md-2">
                        <div className="card-item">
                            <span>
                                Başarı Oranı: %{count?.successInvoiceRate}
                            </span>
                        </div>
                    </div>
                </div>

                <DataTable
                    title="Excel Aktarım Tablosu"
                    fixedHeader={fixedHeader}
                    fixedHeaderScrollHeight={fixedHeaderScrollHeight}
                    columns={[
                        {
                            name: "Aktarım",
                            selector: "logoStatus",
                            sortable: true,
                            background: "red",
                        },
                        {
                            name: "Tür",
                            selector: "type",
                            sortable: true,
                        },
                        {
                            name: "Logo Firması",
                            selector: "company_name",
                            sortable: true,
                        },
                        {
                            name: "Müşteri",
                            selector: "customer_name",
                            sortable: true,
                        },
                        {
                            type: Date,
                            name: "Fatura Tarihi",
                            selector: "invoice_date",
                            format: (row) =>
                                moment(row.invoice_date).format(
                                    "DD.MM.YYYY HH:mm:ss"
                                ),
                            sortable: true,
                        },
                        {
                            type: Date,
                            name: "Oluşturulma Tarihi",
                            selector: "created_at",
                            format: (row) =>
                                moment(row.created_at).format(
                                    "DD.MM.YYYY HH:mm:ss"
                                ),
                            sortable: true,
                        },
                        {
                            name: "Durum",
                            selector: "status",
                            sortable: true,
                        },
                        {
                            name: "Response Message",
                            selector: (row) =>
                                row.status === "200" ? (
                                    row.response_message
                                ) : (
                                    <Tippy
                                        content={row.response_message}
                                        className={classes.tooltip}
                                    >
                                        <div>{row.response_message}</div>
                                    </Tippy>
                                ),
                            sortable: true,
                        },
                        //fatura tetikle
                        // {
                        //     name: "Ftr. Tetikleme",
                        //     cell: (item) => (
                        //         <button
                        //             onClick={() =>
                        //                 props.history.push({
                        //                     pathname: `/urunler/fiyatlandir/${item.id}`,
                        //                 })
                        //             }
                        //             className={"btn btn-warning"}
                        //         >
                        //             Fatura Tetikle
                        //         </button>
                        //     ),
                        // },
                    ]}
                    subHeader={true}
                    responsive={true}
                    hover={true}
                    fixedHeader
                    pagination
                    expandableRows
                    conditionalRowStyles={conditionalRowStyles}
                    expandableRowsComponent={<ExpandedComponent />}
                    data={filter.isFilter ? filter.filteredData : data}
                    // subHeaderComponent={
                    //     <>
                    //         <SearchDialog
                    //             buttonName="Filtre"
                    //             dialogTitle="Fatura Filtreleri"
                    //             open={open}
                    //             handleClickOpen={handleClickOpen}
                    //             handleClose={handleClose}
                    //             actions={
                    //                 <>
                    //                     <Button onClick={handleClose}>
                    //                         İptal
                    //                     </Button>
                    //                     <Button
                    //                         onClick={() => handleSubmit()}
                    //                         autoFocus
                    //                     >
                    //                         Uygula
                    //                     </Button>
                    //                 </>
                    //             }
                    //             dialogContentText={
                    //                 <SearchComponent
                    //                     setTransferStatus={
                    //                         setTransferStatus
                    //                     }
                    //                     setCompanyOf={setCompanyOf}
                    //                     setTypeOf={setTypeOf}
                    //                     beginDate={beginDate}
                    //                     endDate={endDate}
                    //                     handleBeginDateChange={
                    //                         handleBeginDateChange
                    //                     }
                    //                     handleEndDateChange={
                    //                         handleEndDateChange
                    //                     }
                    //                     transferStatus={transferStatusData}
                    //                     typeOf={typeOfData}
                    //                     companyOf={companies}
                    //                     transferStatusLabel={
                    //                         "Aktarım Durumu"
                    //                     }
                    //                     companyOfLabel={"Firma"}
                    //                     typeOfLabel={"Tür"}
                    //                 />
                    //             }
                    //         />

                    //         <TextField
                    //             // filter={filterItem}
                    //             placeholder={"Excel Ara"}
                    //             onChange={filterItem}
                    //             type="text"
                    //             variant="outlined"
                    //             size="small"
                    //             className="ml-1"
                    //         />
                    //     </>
                    // }
                />
            </div>
            {console.log(data)}
        </div>
    );
};
export default ExcelList;
