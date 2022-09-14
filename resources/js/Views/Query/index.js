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


    return (
        <Layout>
            <div className="container">
                <div className="row">
                    <div className="col-md-12" style={{ overflow: "hidden" }}>
                        <DataTable
                            columns={[
                                {
                                    name: "Sorgu Adı",
                                    selector: "name",
                                    width: "333px",
                                    style: {
                                        overflow: "hidden",
                                    },
                                },
                                {
                                    name: "Kısa Kod",
                                    width: "333px",
                                    selector: "code",
                                    style: {
                                        overflow: "hidden",
                                    },
                                },
                                {
                                    width: "920px",
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
                            expandableRows
                            expandableRowsComponent={
                                <ExpandedComponent data={data} />
                            }
                            data={data}
                            subHeaderComponent={
                                <SubHeaderComponent
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
