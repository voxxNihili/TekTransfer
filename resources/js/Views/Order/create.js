import { inject, observer } from "mobx-react";
import React, { useEffect, useState, useRef, memo } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Link } from "react-router-dom";
import {
    Grid,
    List,
    ListItem,
    Typography,
    Card,
    Button,
    Box,
    FormGroup,
} from "@material-ui/core";
import Radio from "@material-ui/core/Radio";
import RadioGroup from "@material-ui/core/RadioGroup";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import FormControl from "@material-ui/core/FormControl";

import { Formik } from "formik";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Select from "react-select";
import ImageUploader from "react-images-upload";
import CKEditor from "ckeditor4-react";
import swal from "sweetalert";
import axios from "axios";
import useStyles from "../../Components/style/theme";
import { Helmet } from "react-helmet";
import Paper from "@mui/material/Paper";

import Checkbox from "@mui/material/Checkbox";
// import { bannerCheckboxStylesHook } from "@mui-treasury/styles/checkbox/banner";

const Create = (props) => {
    const classes = useStyles();
    const { params } = props.location.state.productId;
    const [loading, setLoading] = useState(true);
    const [scriptLoaded, setScriptLoaded] = useState(false);
    const [bool, setBool] = useState(false);
    const [data, setData] = useState([]);
    const [tagDetector, setTagDetector] = useState(false);
    const [iyziScript, setIyziScript] = useState("");
    const [includeScript, setIncludeScript] = useState(false);
    const [numberOfUsers, setNumberOfUsers] = useState([]);
    const [numberOfPeriods, setNumberOfPeriods] = useState([]);
    const [price, setPrice] = useState(0);
    const [userCount, setUserCount] = useState(1);
    const [licencePeriod, setLicencePeriod] = useState(1);

    useEffect(() => {
        settingData();
    }, []);
    useEffect(() => {
        console.log("userCountinsidemyfunction", userCount);
        console.log("licencePeriodinsidemyfunction", licencePeriod);
        var params = {
            productId: props.location.state.productId,
            userLimitId: userCount,
            monthLimitId: licencePeriod,
        };
        axios
            .post(`/api/web/productPrice`, params, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                setPrice(res.data.productPrice)
                console.log("props.match.params", props);
                console.log("res", res);
                console.log("res.productPrice", res.data.productPrice);
            })
            .catch((res) => swal(res.data.message ? res.data.message : "Fiyat Alırken Hata Oluştu."));
    }, [userCount, licencePeriod]);

    const InjectScript = memo(({ script }) => {
        const divRef = useRef(null);

        useEffect(() => {
            if (divRef.current === null) {
                return;
            }
            const doc = document.createRange().createContextualFragment(script);
            divRef.current.innerHTML = "";
            divRef.current.appendChild(doc);
        });

        return <div ref={divRef} />;
    });

    const settingData = async () => {
        await axios
            .get(
                `/api/product/${
                    props.location.state.productId
                        ? props.location.state.productId
                        : props.productId
                } `,
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
                    setData(res?.data.product);
                    setLoading(false);
                } else {
                    swal(res.data.message);
                }
            });
        await axios
            .get(`/api/productUserNumber/`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    let items = res.data.data;
                    setNumberOfUsers(items);
                } else {
                    swal(res.data.data);
                }
            });
        await axios
            .get(`/api/productMonthNumber/`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    let items = res.data.data;
                    setNumberOfPeriods(items);
                } else {
                    swal(res.data.data);
                    setLoading(false);
                }
            });

        // var params = {
        //     userCount: userCount,
        //     licencePeriod: licencePeriod,
        // }
        // await axios
        //     .post(`/web/productPrice`, params, {
        //         headers: {
        //             Authorization:
        //                 "Bearer " +
        //                 props.AuthStore.appState.user.access_token,
        //         },
        //     })
        //     .then((res) => {
        //         // swal(res.data.message);
        //         console.log("initial price", res);
        //     })
        //     .catch((res) => swal(res.data.message));
    };

    const handleUserCount = (e) => {
        setUserCount(parseInt(e.target.value));
    };

    const handleLicencePeriod = (e) => {
        setLicencePeriod(parseInt(e.target.value));
    };
    const handleSubmit = () => {
        var pId = props.location.state.productId
            ? props.location.state.productId
            : props.productId;
        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "application/json",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        axios
            .get("/api/payment/iyzipay/" + pId, config)
            .then((res) => {
                setIyziScript(`${res.data}`);
                setIncludeScript(true);
            })
            .catch((e) => console.log(e));
    };

    return (
        <Layout title={data?.name} description={data?.text}>
            {includeScript && <InjectScript script={iyziScript} />}
            <div className={classes.section}>
                <Link to="/">
                    <Typography>Ürünlere Geri Dön</Typography>
                </Link>
            </div>
            <Grid container spacing={1} justifyContent="space-between">
                <Grid item md={5} xs={12}>
                    {/* {data.image && ( */}
                    <Box
                        component="img"
                        sx={{
                            height: 350,
                            width: 350,
                            maxHeight: { xs: 250, md: 300, lg: 350 },
                            maxWidth: { xs: 250, md: 300, lg: 350 },
                        }}
                        alt={data?.name}
                        src={
                            data?.image
                                ? data.image
                                : "https://www.arraymedical.com/wp-content/uploads/2018/12/product-image-placeholder.jpg"
                        }
                    />
                    {/* )} */}
                </Grid>
                <Grid item md={4} xs={12}>
                    <Paper variant="elevation1" elevation={0}>
                        {" "}
                        <List>
                            {/* <ListItem>
                            <Typography component="h1" variant="h1">
                                {data?.name}
                            </Typography>
                        </ListItem> */}
                            {/* <ListItem>
                <Typography>Category: {data?.category}</Typography>
              </ListItem> */}
                            <ListItem>
                                <Typography>Ürün Adı: {data?.name}</Typography>
                            </ListItem>
                            <ListItem>
                                {/* <Typography>
                                Rating: {data?.rating} stars ({data?.numReviews}{" "}
                                reviews)
                            </Typography> */}
                            </ListItem>
                            <ListItem>
                                <Typography> Açıklama: {data?.text}</Typography>
                            </ListItem>
                        </List>
                    </Paper>
                </Grid>

                <Grid item md={3} xs={12}>
                    <Paper variant="elevation1">
                        <List>
                            <ListItem>
                                <Grid item>
                                    <Typography>Fiyat: {" "}</Typography>
                                </Grid>
                                <Grid item>
                                    <Typography>
                                        {price ? price?.toFixed(2) : " - "} ₺
                                    </Typography>
                                </Grid>
                            </ListItem>
                            <ListItem>
                                <Grid item>
                                    <Typography>Kullanıcı Sayısı</Typography>
                                    <RadioGroup
                                        aria-labelledby="demo-controlled-radio-buttons-group"
                                        name="controlled-radio-buttons-group"
                                        value={userCount}
                                        onChange={handleUserCount}
                                    >
                                        {numberOfUsers.map((item, index) => (
                                            <FormControlLabel
                                                key={index}
                                                value={parseInt(item.number)}
                                                label={
                                                    item.number + " Kullanıcı"
                                                }
                                                control={
                                                    <Radio color="primary" />
                                                }
                                            />
                                            //   <FormControlLabel
                                            //         key={index}
                                            //         value={item.number}
                                            //         control={<Radio/>}
                                            //         label={item.number + " Kullanıcı"}
                                            //     />
                                        ))}
                                    </RadioGroup>
                                </Grid>
                                <Grid item>
                                    <Typography>Lisans Süresi</Typography>
                                    <RadioGroup
                                        aria-labelledby="demo-controlled-radio-buttons-group"
                                        name="controlled-radio-buttons-group"
                                        value={licencePeriod}
                                        onChange={handleLicencePeriod}
                                    >
                                        {numberOfPeriods.map((item, index) => (
                                            <FormControlLabel
                                                key={index}
                                                value={parseInt(item.number)}
                                                label={
                                                    item.number === 0
                                                        ? "Deneme"
                                                        : item.number + " Ay"
                                                }
                                                control={
                                                    <Radio color="primary" />
                                                }
                                            />
                                            //   <FormControlLabel
                                            //         key={index}
                                            //         value={item.number}
                                            //         control={<Radio/>}
                                            //         label={item.number + " Kullanıcı"}
                                            //     />
                                        ))}
                                    </RadioGroup>
                                </Grid>
                            </ListItem>
                            <ListItem>
                                <Button
                                    // disabled={!isValid || isSubmitting}
                                    fullWidth
                                    variant="contained"
                                    color="primary"
                                    onClick={handleSubmit}
                                >
                                    Satın Al
                                </Button>
                            </ListItem>
                        </List>
                    </Paper>
                </Grid>
            </Grid>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Create));
