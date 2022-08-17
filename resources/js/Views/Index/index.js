import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Formik } from "formik";
import * as Yup from "yup";
import axios from "axios";
import { inject, observer } from "mobx-react";
import HomeLayout from "../../Components/Layout/homeLayout";
import { CircleSpinner } from "react-spinners-kit";
// import Products from "../../Components/src/LandingPageProducts";
import ProductTabs from "../../Components/Utils/Tabs";

const Home = (props) => {
    const [errors, setErrors] = useState([]);
    const [error, setError] = useState("");

    // useEffect(() => {
    //     if (props.AuthStore.appState != null) {
    //         if (props.AuthStore.appState.isLoggedIn) {
    //             return props.history.push("/");
    //         }
    //     }
    // });
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        axios
            .post(
                `/api/home`,
                {},
                {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                }
            )
            .then((res) => {
                // setTotal(res.data.total);
                // setStock(res.data.stock);
                // setChartStock(res.data.chartStock);
                // setStockTransaction(res.data.stockTransaction);
                setLoading(false);
            });
    }, []);
    useEffect(() => {
        if (loading)
            return (
                <div className="loading-a">
                    <CircleSpinner size={50} color="#686769" loading={true} />
                </div>
            );
    }, [loading]);

    // const handleSubmit = (values) => {
    //     axios
    //         .post(`/api/auth/login`, { ...values })
    //         .then((res) => {
    //             if (res.data.success) {
    //                 const userData = {
    //                     id: res.data.id,
    //                     name: res.data.name,
    //                     email: res.data.email,
    //                     access_token: res.data.access_token,
    //                 };
    //                 const appState = {
    //                     isLoggedIn: true,
    //                     user: userData,
    //                 };
    //                 props.AuthStore.saveToken(appState);
    //                 //props.history.push('/');
    //                 window.location.reload();
    //             } else {
    //                 alert("Giriş Yapamadınız");
    //             }
    //         })
    //         .catch((error) => {
    //             if (error.response) {
    //                 let err = error.response.data;
    //                 if (err.errors) {
    //                     setErrors(err.errors);
    //                 } else {
    //                     setError(error.response.data.message);
    //                 }
    //                 //alert(err.errors)
    //             } else if (error.request) {
    //                 let err = error.request;
    //                 setError(err);
    //             } else {
    //                 setError(error.message);
    //             }
    //         });
    // };
    // let arr = [];
    // if (errors.length > 0) {
    //     Object.values(errors).forEach((value) => {
    //         arr.push(value);
    //     });
    // }

    return (
        <HomeLayout>
            <ProductTabs />
            {/* <Products /> */}
        </HomeLayout>
    );
};
export default inject("AuthStore")(observer(Home));
