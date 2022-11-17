import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Formik } from "formik";
import * as Yup from "yup";
import axios from "axios";
import { inject, observer } from "mobx-react";
import HomeLayout from "../../Components/Layout/homeLayout";
// import Products from "../../Components/src/LandingPageProducts";
import ProductTabs from "../../Components/src/ProductTabs";
import { Box } from "@mui/material";
import Loading from "/assets/loading.gif";

const Home = (props) => {
    const [errors, setErrors] = useState([]);
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(true);

    // useEffect(() => {
    //     if (loading)
    //         return (
    //             <Box
    //                 component="img"
    //                 src={Loading}
    //                 sx={{
    //                     height: "100%",
    //                     width: "100%",
    //                     // maxHeight: { xs: 233, md: 167 },
    //                     // maxWidth: { xs: 350, md: 250 },
    //                 }}
    //                 alt="loading..."
    //             />
    //         );
    // }, [loading]);

    return (
        <HomeLayout>
            {loading ? (
                <Box
                    component="img"
                    src={Loading}
                    sx={{
                        height: "100%",
                        width: "100%",
                        // maxHeight: { xs: 233, md: 167 },
                        // maxWidth: { xs: 350, md: 250 },
                    }}
                    alt="loading..."
                />
            ) : (
                <ProductTabs />
            )}
        </HomeLayout>
    );
};
export default inject("AuthStore")(observer(Home));
