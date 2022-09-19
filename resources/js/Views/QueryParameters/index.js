import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
const Index = (props) => {
    const [data, setData] = useState([]);
    const [refresh, setRefresh] = useState(false);

    useEffect(() => {
        axios
            .get(`/api/queryParameter`, {
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
            title: "Silmek istediğine emin misin ?",
            text: "Silinince veriler geri gelmicektir",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                axios
                    .delete(`/api/queryParameter/${item.id}`, {
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
            <div className="container">
                <div className="row">
                    <div className="col-md-12">
                        <DataTable
                            columns={[
                                {
                                    name: "Düzenle",
                                    width: "122px",
                                    cell: (item) => (
                                        <button
                                            onClick={() =>
                                                props.history.push({
                                                    pathname: `/sorgu-parametreleri/duzenle/${item.id}`,
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
                                    name: "Parametre",
                                    selector: "parameter",
                                },
                                {
                                    name: "Parametre Adı",
                                    selector: "name",
                                },
                                {
                                    name: "Parametre Türü",
                                    selector: "data_type",
                                },
                            ]}
                            subHeader={true}
                            responsive={true}
                            hover={true}
                            fixedHeader
                            pagination
                            // expandableRows
                            // expandableRowsComponent={<ExpandedComponent />}
                            data={data}
                            subHeaderComponent={
                                <SubHeaderComponent
                                    inputDestroyer={true}
                                    action={{
                                        class: "btn btn-success",
                                        uri: () =>
                                            props.history.push(
                                                "/sorgu-parametreleri/ekle"
                                            ),
                                        title: "Parametre Ekle",
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
