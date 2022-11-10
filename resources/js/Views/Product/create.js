import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Formik } from "formik";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Dropdown from "../../Components/Form/Dropdown";
import Select from "react-select";
import ImageUploader from "react-images-upload";
import CKEditor from "ckeditor4-react";
import swal from "sweetalert";
import axios from "axios";
const Create = (props) => {
    const [categories, setCategories] = useState([]);
    const [userNumbers, setUserNumbers] = useState([]);
    const [monthNumbers, setMonthNumbers] = useState([]);

    const [images, setImages] = useState([]);
    const [property, setProperty] = useState([]);


    useEffect(() => {
        axios
        .get(`/api/product/create`, {
            headers: {
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        })
        .then((res) => {
            // setCategories(res.data.categories);
            console.log("res",res)

        })
        .catch((e) => console.log(e));
        axios
            .get(`/api/productUserNumber`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
            setUserNumbers(res.data.data);
                
                console.log("user",res)
            })
            .catch((e) => console.log(e));
        axios
            .get(`/api/productMonthNumber`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
             setMonthNumbers(res.data.data);

                console.log("month",res)
            })
            .catch((e) => console.log(e));
    }, []);

    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        const data = new FormData();

        images.forEach((image_file) => {
            data.append("file[]", image_file);
        });

        data.append("categoryId", values.categoryId);
        data.append("name", values.name);
        // data.append('modelCode',values.modelCode);
        // data.append('barcode',values.barcode);
        // data.append('brand',values.brand);
        // data.append('tax',values.tax);
        // data.append('stock',values.stock);
        data.append("monthNumberId", values.monthNumberId);
        data.append("userNumberId", values.userNumberId);

        data.append("sellingPrice", values.sellingPrice);
        // data.append('buyingPrice',values.buyingPrice);
        data.append("text", values.text);
        data.append("property", JSON.stringify(property));

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "application/json",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        axios
            .post("/api/product", data, config)
            .then((res) => {
                if (res.data.success) {
                    swal("Ürün Eklendi");
                    resetForm({});
                    setImages([]);
                    setProperty([]);
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

    const newProperty = () => {
        setProperty([...property, { property: "", value: "" }]);
    };

    const removeProperty = (index) => {
        const OldProperty = property;
        OldProperty.splice(index, 1);
        setProperty([...OldProperty]);
    };

    const changeTextInput = (event, index) => {
        console.log(property);
        console.log(event.target.value, index);
        property[index][event.target.name] = event.target.value;
        setProperty([...property]);
    };

    console.log("userNumbers",userNumbers);
    console.log("monthNumbers",monthNumbers);
    return (
        <Layout>
            <div className="mt-5">
                <div className="container">
                    <Formik
                        initialValues={{
                            categoryId: "",
                            name: "",
                            sellingPrice: "",
                            text: "",
                        }}
                        onSubmit={handleSubmit}
                        validationSchema={Yup.object().shape({
                            categoryId: Yup.number().required(
                                "Kategori Seçimi Zorunludur"
                            ),
                            name: Yup.string().required("Ürün Adı Zorunludur"),
                            sellingPrice: Yup.number().required(
                                "Ürün Satış Fiyatı Zorunludur"
                            ),
                            userNumberId: Yup.number().required(
                                "Kullanıcı Sayısı Seçimi Zorunludur"
                            ),
                            monthNumberId: Yup.number().required(
                                "Aylık Zaman Limiti Seçimi Zorunludur"
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
                                        <ImageUploader
                                            withIcon={true}
                                            buttonText="Choose images"
                                            onChange={(picturesFiles) =>
                                                setImages(
                                                    images.concat(picturesFiles)
                                                )
                                            }
                                            imgExtension={[
                                                ".jpg",
                                                ".gif",
                                                ".png",
                                                ".gif",
                                                ".JPG",
                                            ]}
                                            maxFileSize={5242880}
                                            withPreview={true}
                                        />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <div className="form-group">
                                            <Select
                                                onChange={(e) =>
                                                    setFieldValue(
                                                        "categoryId",
                                                        e.id
                                                    )
                                                }
                                                placeholder={
                                                    "Ürün Kategorisi seçiniz *"
                                                }
                                                getOptionLabel={(option) =>
                                                    option.name
                                                }
                                                getOptionValue={(option) =>
                                                    option.id
                                                }
                                                options={categories}
                                            />
                                        </div>
                                        {errors.categoryId &&
                                            touched.categoryId && (
                                                <p className="form-error">
                                                    {errors.categoryId}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="Ürün Adı *"
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
                                            title="Satış Fiyatı *"
                                            type="number"
                                            value={values.sellingPrice}
                                            handleChange={handleChange(
                                                "sellingPrice"
                                            )}
                                        />
                                        {errors.sellingPrice &&
                                            touched.sellingPrice && (
                                                <p className="form-error">
                                                    {errors.sellingPrice}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-6">
                                        <div className="form-group">
                                            <Select
                                                onChange={(e) =>
                                                    setFieldValue(
                                                        "userNumberId",
                                                        e.id
                                                    )
                                                }
                                                placeholder={
                                                    "Kullanıcı Limiti *"
                                                }
                                                getOptionLabel={(option) =>
                                                    option.number
                                                }
                                                getOptionValue={(option) =>
                                                    option.number
                                                }
                                                options={userNumbers}
                                            />
                                        </div>
                                        {errors.userNumberId &&
                                            touched.userNumberId && (
                                                <p className="form-error">
                                                    {errors.userNumberId}
                                                </p>
                                            )}
                                    </div>
                                    <div className="col-md-6">
                                        <div className="form-group">
                                            <Select
                                                onChange={(e) =>
                                                    setFieldValue(
                                                        "monthNumberId",
                                                        e.id
                                                    )
                                                }
                                                placeholder={"Süre Limiti(Ay) *"}
                                                getOptionLabel={(option) =>
                                                    option.number
                                                }
                                                getOptionValue={(option) =>
                                                    option.number
                                                }
                                                options={monthNumbers}
                                            />
                                        </div>
                                        {errors.monthNumberId &&
                                            touched.monthNumberId && (
                                                <p className="form-error">
                                                    {errors.monthNumberId}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <CKEditor
                                            data={values.text}
                                            onChange={(event) => {
                                                const data =
                                                    event.editor.getData();
                                                setFieldValue("text", data);
                                            }}
                                        />
                                    </div>
                                </div>
                                <div className="row mb-3 mt-3">
                                    <div className="col-md-12">
                                        <button
                                            type="button"
                                            onClick={newProperty}
                                            className="btn btn-primary"
                                        >
                                            Yeni Özellik
                                        </button>
                                    </div>
                                </div>
                                {property.map((item, index) => (
                                    <div className="row mb-1">
                                        <div className="col-md-5">
                                            <label>Özellik adı:</label>
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="property"
                                                onChange={(event) =>
                                                    changeTextInput(
                                                        event,
                                                        index
                                                    )
                                                }
                                                value={item.property}
                                            />
                                        </div>
                                        <div className="col-md-5">
                                            <label>Özellik Değeri:</label>
                                            <input
                                                type="text"
                                                className="form-control"
                                                name="value"
                                                onChange={(event) =>
                                                    changeTextInput(
                                                        event,
                                                        index
                                                    )
                                                }
                                                value={item.value}
                                            />
                                        </div>
                                        <div
                                            style={{
                                                display: "flex",
                                                justifyContent: "center",
                                                alignItems: "flex-end",
                                            }}
                                            className="col-md-1"
                                        >
                                            <button
                                                onClick={() =>
                                                    removeProperty(index)
                                                }
                                                type="button"
                                                className="btn btn-danger"
                                            >
                                                X
                                            </button>
                                        </div>
                                    </div>
                                ))}

                                <button
                                    disabled={!isValid || isSubmitting}
                                    onClick={handleSubmit}
                                    className="btn btn-lg btn-primary btn-block"
                                    type="button"
                                >
                                    Ürünü Ekle
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
