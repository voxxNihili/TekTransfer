import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import { Button, TextField } from "@material-ui/core";
import SearchComponent from "../../Components/Utils/SearchComponent";
import SearchDialog from "../../Components/Utils/Dialog";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
import moment from "moment";
import Tippy from "@tippyjs/react";
import useStyles from "../../Components/style/theme";
const Index = (props) => {
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
            .get(`/api/payment/logoCreditCardPaymentList`, {
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
        axios
            .get(`/api/logoCompanyList`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
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
            .get(`/api/payment/logoCreditCardPaymentList`, {
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
            text: "Silinince veriler geri gelmicektir",
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
        <Layout>
            <div className="row">
                <div className="col-md-12" style={{ backgroundColor: "white" }}>
                    <div className="container">
                        <div className="row">
                            <div className="col-md-3">
                                <div className="card-item">
                                    <span>
                                        Başarılı Aktarım Sayısı :{" "}
                                        {count?.successPayment}
                                    </span>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="card-item">
                                    <span>
                                        Başarısız Aktarım Sayısı1 :{" "}
                                        {count?.failedPayment}
                                    </span>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="card-item">
                                    <span>
                                        Toplam Aktarım Sayısı :{" "}
                                        {count?.totalPayment}
                                    </span>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="card-item">
                                    <span>
                                        Başarı Oranı : %
                                        {count?.successPaymentRate}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <DataTable
                        columns={[
                            {
                                name: "Durum",
                                selector: "status",
                                sortable: true,
                                // width: "100px",
                            },
                            {
                                name: "Firma",
                                selector: "company_id",
                                sortable: true,
                                // width: "150px",
                            },
                            {
                                name: "Müşteri Kodu",
                                selector: "current_id",
                                sortable: true,
                                // width: "150px",
                            },
                            {
                                name: "Tutar",
                                selector: (row) => {
                                    return row.price?.toFixed(2) + " ₺";
                                },
                                sortable: true,
                                // width: "150px",
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
                                // width: "250px",
                            },

                            {
                                name: (
                                    <Tippy content="Response Message">
                                        <div>Response Message</div>
                                    </Tippy>
                                ),

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
                                // width: "500px",
                            },
                        ]}
                        subHeader={true}
                        responsive={true}
                        hover={true}
                        fixedHeader
                        conditionalRowStyles={conditionalRowStyles}
                        pagination
                        expandableRows
                        expandableRowsComponent={<ExpandedComponent />}
                        data={filter.isFilter ? filter.filteredData : data}
                        subHeaderComponent={
                            <>
                                <SearchDialog
                                    buttonName="Filtre"
                                    dialogTitle="Fatura Filtreleri"
                                    open={open}
                                    handleClickOpen={handleClickOpen}
                                    handleClose={handleClose}
                                    actions={
                                        <>
                                            <Button onClick={handleClose}>
                                                İptal
                                            </Button>
                                            <Button
                                                onClick={() => handleSubmit()}
                                                autoFocus
                                            >
                                                Uygula
                                            </Button>
                                        </>
                                    }
                                    dialogContentText={
                                        <SearchComponent
                                            setTransferStatus={
                                                setTransferStatus
                                            }
                                            setCompanyOf={setCompanyOf}
                                            setTypeOf={setTypeOf}
                                            beginDate={beginDate}
                                            endDate={endDate}
                                            handleBeginDateChange={
                                                handleBeginDateChange
                                            }
                                            handleEndDateChange={
                                                handleEndDateChange
                                            }
                                            transferStatus={transferStatusData}
                                            typeOf={typeOfData}
                                            companyOf={companies}
                                            transferStatusLabel={
                                                "Aktarım Durumu"
                                            }
                                            companyOfLabel={"Firma"}
                                            typeOfLabel={"Tür"}
                                        />
                                    }
                                />

                                <TextField
                                    // filter={filterItem}
                                    placeholder={"Müşteri Ara"}
                                    onChange={filterItem}
                                    type="text"
                                    variant="outlined"
                                    size="small"
                                    className="ml-1"
                                />
                            </>
                        }
                    />
                </div>
                {console.log(data)}
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
