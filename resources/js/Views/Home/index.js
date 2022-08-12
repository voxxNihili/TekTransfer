import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Formik } from "formik";
import * as Yup from "yup";
import axios from "axios";
import { inject, observer } from "mobx-react";
import HomeLayout from "../../Components/Layout/homeLayout";

const Home = (props) => {
    const [errors, setErrors] = useState([]);
    const [error, setError] = useState("");

    useEffect(() => {
        if (props.AuthStore.appState != null) {
            if (props.AuthStore.appState.isLoggedIn) {
                return props.history.push("/");
            }
        }
    });

    const handleSubmit = (values) => {
        axios
            .post(`/api/auth/login`, { ...values })
            .then((res) => {
                if (res.data.success) {
                    const userData = {
                        id: res.data.id,
                        name: res.data.name,
                        email: res.data.email,
                        access_token: res.data.access_token,
                    };
                    const appState = {
                        isLoggedIn: true,
                        user: userData,
                    };
                    props.AuthStore.saveToken(appState);
                    //props.history.push('/');
                    window.location.reload();
                } else {
                    alert("Giriş Yapamadınız");
                }
            })
            .catch((error) => {
                if (error.response) {
                    let err = error.response.data;
                    if (err.errors) {
                        setErrors(err.errors);
                    } else {
                        setError(error.response.data.message);
                    }
                    //alert(err.errors)
                } else if (error.request) {
                    let err = error.request;
                    setError(err);
                } else {
                    setError(error.message);
                }
            });
    };
    let arr = [];
    if (errors.length > 0) {
        Object.values(errors).forEach((value) => {
            arr.push(value);
        });
    }
    return (
        <HomeLayout>
            <div>
              aaa
            </div>
        </HomeLayout>
    );
};
export default inject("AuthStore")(observer(Home));
