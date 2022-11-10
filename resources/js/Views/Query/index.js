import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
import "../../../css/style.css";

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
            .get(`/api/query`, {
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
    const deleteItem = (item) => {
        swal({
            title: "Silmek istediğinize emin misiniz?",
            text: "Silinince veriler geri gelmeyecektir!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                axios
                    .delete(`/api/query/${item.id}`, {
                        headers: {
                            Authorization:
                                "Bearer " +
                                props.AuthStore.appState.user.access_token,
                        },
                    })
                    .then((res) => {
                        if (res.data.success) {
                            swal(res.data.message);
                            setRefresh(!refresh);
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
            <div className="container">
                <div className="row">
                    <div className="col-md-12 queryTable">
                        <DataTable
                            columns={[
                                {
                                    name: "Düzenle",
                                    width: "122px",
                                    cell: (item) => (
                                        <button
                                            onClick={() =>
                                                props.history.push({
                                                    pathname: `/sorgular/duzenle/${item.id}`,
                                                })
                                            }
                                            className={"btn btn-primary"}
                                        >
                                            Düzenle
                                        </button>
                                    ),
                                },
                                {
                                    name: "Sil",
                                    width: "111px",
                                    cell: (item) => (
                                        <button
                                            onClick={() => deleteItem(item)}
                                            className={"btn btn-danger"}
                                        >
                                            Sil
                                        </button>
                                    ),
                                    button: true,
                                },
                                {
                                    name: "Sorgu Adı",
                                    selector: "name",
                                    width: "111px",
                                    style: {
                                        overflow: "hidden",
                                    },
                                },
                                {
                                    name: "Kısa Kod",
                                    width: "111px",
                                    selector: "code",
                                    style: {
                                        overflow: "hidden",
                                    },
                                },
                                {
                                    // width: "100%",
                                    name: "Sorgu",
                                    selector: "sqlQuery",
                                    style: {
                                        overflow: "hidden",
                                    },
                                },
                            ]}
                            allowOverflow={false}
                            subHeader={true}
                            responsive={true}
                            hover={true}
                            fixedHeader
                            pagination
                            // expandableRows
                            // expandableRowsComponent={
                            //     <ExpandedComponent data={data} />
                            // }
                            data={data}
                            subHeaderComponent={
                                <SubHeaderComponent
                                    inputDestroyer={true}
                                    action={{
                                        class: "btn btn-success",
                                        uri: () =>
                                            props.history.push(
                                                "/sorgular/ekle"
                                            ),
                                        title: "Sorgu Ekle",
                                    }}
                                />
                            }
                        />
                    </div>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
