import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Formik } from "formik";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Select from "react-select";
import ImageUploader from "react-images-upload";
import CKEditor from "ckeditor4-react";
import swal from "sweetalert";
import axios from "axios";
import { difference, values } from "lodash";
import { Form } from "react-bootstrap";
import DynamicTable from "./table";
const Edit = (props) => {
    const { params } = props.match;
    const [loading, setLoading] = useState(true);
    const [table, setTable] = useState(false);
    const [queries, setQueries] = useState([]);
    const [formData, updateFormData] = useState([]);
    const [dataTable, setDataTable] = useState([]);
    console.log("params", params);

    useEffect(() => {
        console.log(params);
        axios
            .get(`/api/report/${params.id} `, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    setQueries(res.data.data[0].query_param);
                    setLoading(false);
                    console.log(5587, queries);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((e) => console.log(e));
    }, []);

    const handleChange = (e) => {
        updateFormData({
            ...formData,

            [e.target.id]: e.target.value.trim(),
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        const data = new FormData();
        data.append("license", "MNKCF-8HV9R-ALK2D-LHC4B");
        data.append("query", JSON.stringify(formData));

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        console.log(data);
        axios
            .post(`/api/queryApi/${params.id}`, data, config)
            .then((res) => {

                console.log(7777,res.data.data)
                if (res.data.success) {
                    setDataTable(res.data.data);
                    // console.log('dssd',dataTable);
                    setTable(true)
                    setLoading(false);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((e) => console.log(e));

            
    };

  


    if (loading) return <div>Yükleniyor</div>;

    return (
        <Layout>
            {console.log("queries", queries)}
            {console.log("dataTable", dataTable)}
            
            <div className="row">
                <div>
                    <Form>
                        <div className="row">
                            {queries.map((item) => (
                                <div className="col-4">
                                    {item.parameter[0].name}
                                    <input
                                        name={item.parameter[0].name}
                                        id={item.parameter[0].parameter}
                                        type={item.parameter[0].data_type}
                                        onChange={handleChange}
                                    />
                                </div>
                            ))}
                            <button className="col-4" onClick={handleSubmit}>
                                Submit
                            </button>
                        </div>
                    </Form>
                </div>
            </div>
            {table == true &&
            <div className="row" style={{marginTop:'10px'}}> 
                <DynamicTable dataTable={dataTable}/>
            </div>
               
            }

            

        </Layout>
    );
};
export default inject("AuthStore")(observer(Edit));
