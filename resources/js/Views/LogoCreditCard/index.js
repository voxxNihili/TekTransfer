import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
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
    const [filter, setFilter] = useState({
        filteredData: [],
        text: "",
        isFilter: false,
    });

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
                                        Başarısız Aktarım Sayısı :{" "}
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
                                width: "100px",
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
                                width: "500px",
                            },

                            {
                                name: "Tutar",
                                selector: (row) => {
                                    return row.price?.toFixed(2) + " ₺";
                                },
                                sortable: true,
                                width: "150px",
                            },
                            {
                                name: "Müşteri Kodu",
                                selector: "current_id",
                                sortable: true,
                                width: "150px",
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
                                width: "250px",
                            },
                            {
                                name: "Tür",
                                selector: "type",
                                sortable: true,
                                width: "150px",
                            },
                            {
                                name: "Firma",
                                selector: "company_id",
                                sortable: true,
                                width: "150px",
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
                            <SubHeaderComponent
                                filter={filterItem}
                            />
                        }
                    />
                </div>
                {console.log(data)}
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
