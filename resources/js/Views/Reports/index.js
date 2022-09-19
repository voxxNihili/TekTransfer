import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
// import { Route , Switch} from 'react-router-dom';
// import PrivateRoute from './PrivateRoute';
import { useHistory, Link, Redirect } from "react-router-dom";
import { Route, Switch } from "react-router-dom";

import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
import "../../../css/style.css";

const Index = (props) => {
    const [redirState, setState] = useState(false);
    const [shortName, setShortName] = useState("");

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
    const handleRowClicked = () => {
        console.log("rowclicked");
    };
    //   let redirecting = redirState ? console.log() : '';

    return (
        <Layout>
            <div className="container">
                <div className="row">
                    <div className="col-md-12 queryTable">
                        <DataTable
                            columns={[
                                {
                                    name: "Raporlar",
                                    selector: "name",
                                    // width: "111px",
                                    // style: {
                                    //     overflow: "hidden",
                                    // },
                                },
                            ]}
                            allowOverflow={false}
                            subHeader={true}
                            responsive={true}
                            hover={true}
                            onRowClicked={(rowData) => {
                                console.log("rowdata", rowData.code);
                                props.history.push({
                                    pathname: `/raporlar/${rowData.code}`,
                                });
                            }}
                            pointerOnHover={true}
                            highlightOnHover={true}
                            theme="light"
                            fixedHeader
                            pagination
                            // expandableRows
                            // expandableRowsComponent={
                            //     <ExpandedComponent data={data} />
                            // }
                            data={data}
                            // subHeaderComponent={
                            //     <SubHeaderComponent
                            //         inputDestroyer={true}
                            //         action={{
                            //             class: "btn btn-success",
                            //             uri: () =>
                            //                 props.history.push(
                            //                     "/sorgular/ekle"
                            //                 ),
                            //             title: "Sorgu Ekle",
                            //         }}
                            //     />
                            // }
                        />
                    </div>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
