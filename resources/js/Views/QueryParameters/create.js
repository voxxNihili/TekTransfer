import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Formik } from "formik";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Dropdown from "../../Components/Form/Dropdown";
import Select from "react-select";
// import ImageUploader from 'react-images-upload';
import CKEditor from "ckeditor4-react";
import swal from "sweetalert";
import axios from "axios";
const Create = (props) => {
    const [parameters, setParameters] = useState([]);
    // const [images,setImages] = useState([]);
    const [property, setProperty] = useState([]);

    useEffect(() => {
        axios.get(`/api/queryParameter/create`, {
            headers: {
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        });
        // .then((res) => {
        //     setParameters(res.data.parameter);
        // })
        // .catch((e) => console.log(e));
    }, []);
    const options = [
        { label: "String", value: "string", key: "1" },
        { label: "dateTime", value: "date-time", key: "1" },
        { label: "Int", value: "vegetable", key: "2" },
    ];

    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        const data = new FormData();

        data.append("parameter", values.parameter);
        data.append("name", values.name);
        data.append("data_type", values.data_type);

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        axios
            .post("/api/queryParameter", data, config)
            .then((res) => {
                if (res.data.success) {
                    swal("Parametre Eklendi");
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
                <div className="container">
                    <Formik
                        initialValues={{
                            parameter: "",
                            name: "",
                            data_type: "",
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
                                            title="Parametre *"
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
                                    <div className="col-md-12">
                                        <CustomInput
                                            title="Parametre Adı *"
                                            value={values.name}
                                            handleChange={handleChange("name")}
                                        />
                                        {errors.name && touched.name && (
                                            <p className="form-error">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>
                                    <div className="col-md-12">
                                        <Dropdown
                                            label="Parametre Veri Türü *"
                                            options={options}
                                            key={options.key}
                                            value={values.data_type}
                                            onChange={handleChange(
                                                "data_type"
                                            )}
                                        />
                                    </div>
                                    {/* <div className="col-md-12">
                                        <CustomInput
                                            type="date"
                                            title="Parametre Türü *"
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
                                    </div>{" "}
                                    <p>{values.data_type_options}!</p> */}
                                </div>

                                <button
                                    disabled={!isValid || isSubmitting}
                                    onClick={handleSubmit}
                                    className="btn btn-lg btn-primary btn-block"
                                    type="button"
                                >
                                    Parametre Ekle
                                </button>
                            </div>
                        )}
                    </Formik>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Create));
