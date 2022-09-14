import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Formik } from "formik";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Select from "react-select";
// import ImageUploader from 'react-images-upload';
import CKEditor from "ckeditor4-react";
import swal from "sweetalert";
import axios from "axios";
const Create = (props) => {
    const [categories, setCategories] = useState([]);
    // const [images,setImages] = useState([]);
    const [selectedRows, setSelectedRows] = useState([]);
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

    useEffect(() => {
        axios
            .get(`/api/query/create`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                setCategories(res.data.categories);
            })
            .catch((e) => console.log(e));
    }, []);
    const handleChange = ({ selectedRows }) => {
        setSelectedRows(selectedRows);
        console.log("selectedRows", selectedRows);
    };
    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        const data = new FormData();

        data.append("name", values.name);
        data.append("code", values.code);
        data.append("sqlQuery", values.sqlQuery);
        data.append("selectedRows", JSON.stringify(selectedRows));

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        // console.log(data.code)
        axios
            .post("/api/query", data, config)
            .then((res) => {
                if (res.data.success) {
                    swal("Sorgu Eklendi");
                    resetForm({});
                    setSubmitting(false);
                } else {
                    swal(res.data.message);
                    setSubmitting(true);
                }
            })
            .catch((e) => {
                setSubmitting(true);
                console.log(e);
            });
    };

    return (
        <Layout>
            <div className="mt-5">
                <div className="row container">
                    <div className="col-md-12 ">
                        <Formik
                            initialValues={{
                                name: "",
                                code: "",
                                sqlQuery: "",
                            }}
                            onSubmit={handleSubmit}
                            validationSchema={Yup.object().shape({
                                name: Yup.string().required(
                                    "Sorgu Adı Zorunludur"
                                ),
                                code: Yup.string().required(
                                    "Kısa Kod Zorunludur"
                                ),
                                sqlQuery:
                                    Yup.string().required("Sorgu Zorunludur"),
                            })}
                        >
                            {({
                                values,
                                handleChange,
                                handleSubmit,
                                handleBlur,
                                errors,
                                isValid,
                                isSubmitting,
                                setFieldValue,
                                touched,
                            }) => (
                                <div>
                                    <div className="row">
                                        <div className="col-md-12">
                                            <CustomInput
                                                title="Sorgu Adı *"
                                                value={values.name}
                                                handleChange={handleChange(
                                                    "name"
                                                )}
                                            />
                                            {errors.name && touched.name && (
                                                <p className="form-error">
                                                    {errors.name}
                                                </p>
                                            )}
                                        </div>
                                        <div className="col-md-12">
                                            <CustomInput
                                                title="Kısa Kod *"
                                                value={values.code}
                                                handleChange={handleChange(
                                                    "code"
                                                )}
                                            />
                                            {errors.code && touched.code && (
                                                <p className="form-error">
                                                    {errors.code}
                                                </p>
                                            )}
                                        </div>
                                        <div className="col-md-12">
                                            <CustomInput
                                                title="Sorgu *"
                                                value={values.sqlQuery}
                                                handleChange={handleChange(
                                                    "sqlQuery"
                                                )}
                                            />
                                            {errors.sqlQuery &&
                                                touched.sqlQuery && (
                                                    <p className="form-error">
                                                        {errors.sqlQuery}
                                                    </p>
                                                )}
                                        </div>
                                    </div>

                                    <button
                                        disabled={!isValid || isSubmitting}
                                        onClick={handleSubmit}
                                        className="btn btn-lg btn-primary btn-block"
                                        type="button"
                                    >
                                        Sorgu Ekle
                                    </button>
                                </div>
                            )}
                        </Formik>
                    </div>
                    <div className="col-md-12 m-5">
                        <DataTable
                            columns={[
                                {
                                    name: "PARAMETRE",
                                    selector: "parameter",
                                    width: "auto",
                                },
                                {
                                    name: "PARAMETRE ADI",
                                    selector: "name",
                                    width: "auto",

                                },
                                {
                                    name: "PARAMETRE TÜRÜ",
                                    selector: "data_type",
                                    width: "auto",
                                },
                            ]}
                            width="auto"
                            maxWidth={"auto"}
                            subHeader={true}
                            responsive={true}
                            // hover={true}
                            // onRowClicked={clickHandler}
                            // fixedHeader
                            selectableRows
                            onSelectedRowsChange={handleChange}
                            // clearSelectedRows={toggledClearRows}
                            // expandableRows
                            // expandableRowsComponent={<ExpandedComponent />}
                            data={data}
                            subHeaderComponent={
                                <div
                                    className="row"
                                    style={{
                                        textAlign: "start",
                                        margin: "auto",
                                    }}
                                >
                                    Parametreler
                                </div>
                            }
                        />
                    </div>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Create));
