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
const Create = (props) => {
    const [products, setProducts] = useState([]);
    const [stockTypes, setStockTypes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [accounts, setAccounts] = useState([]);

    useEffect(() => {
        axios
            .get(`/api/stock/create`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                console.log(res);
                setProducts(res.data.products);
                setStockTypes(res.data.stockTypes);
                setLoading(false);
            })
            .catch((e) => console.log(e));
    }, []);

    const changeStockType = (stockType) => {
        axios
            .post(
                `/api/stock/get-customer`,
                { stockType },
                {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                }
            )
            .then((res) => {
                setAccounts(res.data.customers);
            });
    };

    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        axios
            .post(
                "/api/stock",
                { ...values },
                {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                }
            )
            .then((res) => {
                if (res.data.success) {
                    resetForm({});
                    setAccounts([]);
                    swal(res.data.message);
                    setSubmitting(false);
                } else {
                    swal(res.data.message);
                    setSubmitting(false);
                }
            })
            .catch((e) => setSubmitting(false));
    };

    if (loading) return <div>Y??kleniyor</div>;

    return (
        <Layout>
            <div className="mt-5">
               
                    <Formik
                        initialValues={{
                            stockType: "",
                            customerId: 0,
                            productId: "",
                            quantity: "",
                            totalPrice: "",
                            date: "",
                            note: "",
                            isStock: false,
                        }}
                        onSubmit={handleSubmit}
                        validationSchema={Yup.object().shape({
                            stockType: Yup.number().required(
                                "????lem Se??imi Zorunludur"
                            ),
                            productId: Yup.number().required(
                                "??r??n Se??imi Zorunludur"
                            ),
                            quantity: Yup.number().required(
                                "Stok Adeti  Zorunludur"
                            ),
                            totalPrice:
                                Yup.number().required("Fiyat  Zorunludur"),
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
                                        <div className="form-group">
                                            <Select
                                                onChange={(e) => {
                                                    changeStockType(e.id);
                                                    setFieldValue(
                                                        "stockType",
                                                        e.id
                                                    );
                                                }}
                                                placeholder={
                                                    "????lem Tipi se??iniz *"
                                                }
                                                getOptionLabel={(option) =>
                                                    option.name
                                                }
                                                getOptionValue={(option) =>
                                                    option.id
                                                }
                                                options={stockTypes}
                                            />
                                        </div>
                                        {errors.stockType &&
                                            touched.stockType && (
                                                <p className="form-error">
                                                    {errors.stockType}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <div className="form-group">
                                            <Select
                                                onChange={(e) =>
                                                    setFieldValue(
                                                        "customerId",
                                                        e.id
                                                    )
                                                }
                                                placeholder={"Hesap se??iniz"}
                                                getOptionLabel={(option) =>
                                                    option.name
                                                }
                                                getOptionValue={(option) =>
                                                    option.id
                                                }
                                                options={accounts}
                                            />
                                        </div>
                                        {errors.customerId &&
                                            touched.customerId && (
                                                <p className="form-error">
                                                    {errors.customerId}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <div className="form-group">
                                            <Select
                                                onChange={(e) =>
                                                    setFieldValue(
                                                        "productId",
                                                        e.id
                                                    )
                                                }
                                                placeholder={"??r??n  se??iniz *"}
                                                getOptionLabel={(option) =>
                                                    `${option.modelCode} - ${option.name}`
                                                }
                                                getOptionValue={(option) =>
                                                    option.id
                                                }
                                                options={products}
                                            />
                                        </div>
                                        {errors.productId &&
                                            touched.productId && (
                                                <p className="form-error">
                                                    {errors.productId}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="??r??n Stok Adeti *"
                                            type="number"
                                            value={values.quantity}
                                            handleChange={handleChange(
                                                "quantity"
                                            )}
                                        />
                                        {errors.quantity &&
                                            touched.quantity && (
                                                <p className="form-error">
                                                    {errors.quantity}
                                                </p>
                                            )}
                                    </div>
                                    <div className="col-md-6">
                                        <CustomInput
                                            title="Toplam Fiyat *"
                                            type="number"
                                            value={values.totalPrice}
                                            handleChange={handleChange(
                                                "totalPrice"
                                            )}
                                        />
                                        {errors.totalPrice &&
                                            touched.totalPrice && (
                                                <p className="form-error">
                                                    {errors.totalPrice}
                                                </p>
                                            )}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <CustomInput
                                            type="date"
                                            value={values.date}
                                            handleChange={handleChange("date")}
                                        />
                                        {errors.date && touched.date && (
                                            <p className="form-error">
                                                {errors.date}
                                            </p>
                                        )}
                                    </div>
                                </div>
                                <div className="row mb-2">
                                    <div className="col-md-12">
                                        <input
                                            checked={values.isStock}
                                            type="checkbox"
                                            onChange={handleChange("isStock")}
                                        />
                                        <label className="ml-1">
                                            Stokta??a yans??t??ls??n m?? ?
                                        </label>
                                    </div>
                                </div>
                                <div className="row mb-2">
                                    <div className="col-md-12">
                                        <CKEditor
                                            data={values.note}
                                            onChange={(event) => {
                                                const data =
                                                    event.editor.getData();
                                                setFieldValue("note", data);
                                            }}
                                        />
                                    </div>
                                </div>

                                <button
                                    disabled={!isValid || isSubmitting}
                                    onClick={handleSubmit}
                                    className="btn btn-lg btn-primary btn-block"
                                    type="button"
                                >
                                    ????lemi Kaydet
                                </button>
                            </div>
                        )}
                    </Formik>
                 
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Create));
