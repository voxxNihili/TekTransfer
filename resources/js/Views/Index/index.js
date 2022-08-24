import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Formik } from "formik";
import * as Yup from "yup";
import axios from "axios";
import { inject, observer } from "mobx-react";
import HomeLayout from "../../Components/Layout/homeLayout";
import { CircleSpinner } from "react-spinners-kit";
// import Products from "../../Components/src/LandingPageProducts";
import ProductTabs from "../../Components/src/ProductTabs";

const Home = (props) => {
    const [errors, setErrors] = useState([]);
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (loading)
            return (
                <div className="loading-a">
                    <CircleSpinner size={50} color="#686769" loading={true} />
                </div>
            );
    }, [loading]);

    return (
        <HomeLayout>
            <ProductTabs />
            {/* <Products /> */}
        </HomeLayout>
    );
};
export default inject("AuthStore")(observer(Home));
