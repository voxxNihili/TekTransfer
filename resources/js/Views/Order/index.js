import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
import moment from "moment";
const Index = (props) => {
    const [data, setData] = useState([]);
    const [refresh, setRefresh] = useState(false);
    const [filter, setFilter] = useState({
        filteredData: [],
        text: "",
        isFilter: false,
    });

    useEffect(() => {
        axios
            .get(`/api/order`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                setData(res.data.data);
            })
            .catch((e) => console.log(e));
    }, [refresh]);

    const filterItem = (e) => {
        const filterText = e.target.value;
        if (filterText != "") {
            const filteredItems = data.filter(
                (item) =>
                    (item.name &&
                        item.name
                            .toLowerCase()
                            .includes(filterText.toLowerCase())) ||
                    (item.barcode &&
                        item.barcode
                            .toLowerCase()
                            .includes(filterText.toLowerCase())) ||
                    (item.modelCode &&
                        item.modelCode
                            .toLowerCase()
                            .includes(filterText.toLowerCase()))
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
            title: "Silmek istedi??ine emin misin ?",
            text: "Silinince veriler geri gelmicektir",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                axios
                    .delete(`/api/order/${item.id}`, {
                        headers: {
                            Authorization:
                                "Bearer " +
                                props.AuthStore.appState.user.access_token,
                        },
                    })
                    .then((res) => {
                        if (res.data.success) {
                            setRefresh(true);
                        } else {
                            swal(res.data.message);
                        }
                    })
                    .catch((e) => console.log(e));
            }
        });
    };

    return (
        <Layout>
            <div className="row">
                <div className="col-md-12">
                    <DataTable
                        columns={[
                            {
                                name: "Sipari?? Kodu",
                                selector: "orderCode",
                                sortable: true,
                            },
                            {
                                name: "PC",
                                selector: "license[0].pcName",
                                sortable: true,
                            },
                            {
                                name: "IP",
                                selector: "license[0].ip",
                                sortable: true,
                            },
                            {
                                name: "??r??n Anahtar??",
                                selector: "license[0].licenseKey",
                                sortable: true,
                            },
                            {
                                type: Date,
                                name: "Lisans Ba??lang??c??",
                                selector: "license[0].startDate",
                                format: (row) =>
                                    moment(row.license[0].startDate).format(
                                        "DD.MM.YYYY"
                                    ),
                                sortable: true,
                            },
                            {
                                type: Date,
                                name: "Lisans Biti??i",
                                selector: "license[0].endDate",
                                format: (row) =>
                                    moment(row.license[0].endDate).format(
                                        "DD.MM.YYYY"
                                    ),
                                sortable: true,
                            },
                            // {
                            //     name:'D??zenle',
                            //     cell:(item) => <button onClick={() => props.history.push(({
                            //         pathname: `/urunler/duzenle/${item.id}`
                            //     }))} className={"btn btn-primary"}>D??zenle</button>
                            // },
                            {
                                name: "Ayarla",
                                cell: (item) => (
                                    <>
                                    {console.log("item",item)}
                                    <button
                                        onClick={() =>
                                            props.history.push({
                                                pathname: `/siparisler/firma-sec`,
                                                state: {
                                                    orderId:item.license[0].licenseKey,
                                                    accountLimit:item.license[0].accountLimit,
                                                    userId: item.userId,
                                                    licenseId: item.licenseId,
                                                    companiesOfLicense: item.license[0].company
                                                },
                                            })
                                        }
                                        className={"btn btn-danger"}
                                    >
                                        Ayarla
                                    </button></>
                                ),
                            },
                        ]}
                        subHeader={true}
                        responsive={true}
                        hover={true}
                        fixedHeader
                        pagination
                        expandableRows
                        expandableRowsComponent={<ExpandedComponent />}
                        data={filter.isFilter ? filter.filteredData : data}
                        subHeaderComponent={
                            <SubHeaderComponent
                                filter={filterItem}
                                action={{
                                    class: "btn btn-success",
                                    uri: () =>
                                        props.history.push("/urunler/ekle"),
                                    title: "Yeni ??r??n Ekle",
                                }}
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
