import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Formik } from "formik";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import swal from "sweetalert";
import axios from "axios";

const Edit = (props) => {
    const { params } = props.match;
    console.log("params",params)
    const [loading, setLoading] = useState(true);
    const [queryParameters, setQueryParameters] = useState([]);
    useEffect(() => {
        axios
            .get(`/api/queryParameter/${params.id} `, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    setQueryParameters(res.data.query);
                    setLoading(false);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((e) => console.log(e));
    }, []);

    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        console.log("aslfkşasldkf");
        const data = new FormData();
        data.append("parameter", values.parameter);
        data.append("name", values.name);
        data.append("data_type", values.data_type);
        data.append("_method", "put");

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };

        axios
            .post(`/api/queryParameter/${queryParameters.id}`, data, config)
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
            
            {console.log("queryParameters", queryParameters)}
            <div className="mt-5">
                <div className="container">
                    <Formik
                        initialValues={{
                            parameter: queryParameters.parameter,
                            name: queryParameters.name,
                            data_type: queryParameters.data_type,
                        }}
                        onSubmit={handleSubmit}
                        validationSchema={Yup.object().shape({
                            parameter: Yup.string().required(
                                "Parametre Zorunludur"
                            ),
                            name: Yup.string().required(
                                "Parametre Adı Zorunludur"
                            ),
                            data_type: Yup.string().required(
                                "Parametre Türü Zorunludur"
                            ),
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
                                            title="Parametre"
                                            value={values.parameter}
                                            handleChange={handleChange(
                                                "parameter"
                                            )}
                                        />
                                        {errors.parameter &&
                                            touched.parameter && (
                                                <p className="form-error">
                                                    {errors.parameter}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="Parametre Adı"
                                            value={values.name}
                                            handleChange={handleChange("name")}
                                        />
                                        {errors.name && touched.name && (
                                            <p className="form-error">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="Parametre Türü"
                                            value={values.data_type}
                                            handleChange={handleChange(
                                                "data_type"
                                            )}
                                        />
                                        {errors.data_type &&
                                            touched.data_type && (
                                                <p className="form-error">
                                                    {errors.data_type}
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
