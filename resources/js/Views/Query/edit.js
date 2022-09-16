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
import { difference } from "lodash";
const Edit = (props) => {
    const { params } = props.match;
    const [loading, setLoading] = useState(true);
    const [queries, setQueries] = useState([]);
    useEffect(() => {
        axios
            .get(`/api/query/${params.id} `, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    setQueries(res.data.query);
                    setLoading(false);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((e) => console.log(e));
    }, []);

    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        console.log("aslfkşasldkf")
        const data = new FormData();
        
        data.append("name", values.name);
        data.append("code", values.code);
        data.append("sqlQuery", values.sqlQuery);
        data.append('_method','put');

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };

        console.log(111,data);
        console.log(333,config);
        
        axios
            .post(`/api/query/${queries.id}`, data, config)
            .then((res) => {
                if (res.data.success) {
                    setSubmitting(false);
                    swal(res.data.message);
                } else {
                    swal(res.data.message);
                    setSubmitting(false);
                }
            })
            .catch((e) => console.log(e));
    };

    if (loading) return <div>Yükleniyor</div>;

    return (
        <Layout>
            {console.log("queries", queries)}
            <div className="mt-5">
                <div className="container">
                    <Formik
                        initialValues={{
                            name: queries.name,
                            code: queries.code,
                            sqlQuery: queries.sqlQuery,
                        }}
                        onSubmit={handleSubmit}
                        validationSchema={Yup.object().shape({
                            name: Yup.string().required("Sorgu Adı Zorunludur"),
                            code: Yup.string().required("Kısa Kod Zorunludur"),
                            sqlQuery: Yup.string().required("Sorgu Zorunludur"),
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
                                            title="Sorgu Adı"
                                            value={values.name}
                                            handleChange={handleChange("name")}
                                        />
                                        {errors.name && touched.name && (
                                            <p className="form-error">
                                                {errors.name}
                                            </p>
                                        )}
                                  
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="Kısa Kod"
                                            value={values.code}
                                            handleChange={handleChange("code")}
                                        />
                                        {errors.code && touched.code && (
                                            <p className="form-error">
                                                {errors.code}
                                            </p>
                                        )}
                                    </div>
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="Sorgu"
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
                                    Ürünü Düzenle
                                </button>
                            </div>
                        )}
                    </Formik>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Edit));
