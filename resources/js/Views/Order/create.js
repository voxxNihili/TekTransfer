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
} from "@material-ui/core";
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

const Create = (props) => {
    const classes = useStyles();
    const { params } = props.location.state.productId;
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState([]);
    const [iyziScript, setIyziScript] = useState("");
    const [includeScript, setIncludeScript] = useState(false);

    useEffect(() => {
        settingData();
    }, []);

    const InjectScript = memo(({ script }) => {
        const divRef = useRef(null);

        useEffect(() => {
            if (divRef.current === null) {
                return;
            }
            const range = document.createRange();
            range.selectNode(document.getElementsByClassName("script-context").item(0));

            // create a contextual fragment that will execute the script
            // beware of security concerns!!
            const doc = range.createContextualFragment(script);

            // clear the div HTML, and append the doc fragment with the script
            // divRef.current.innerHTML = "";
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
                console.log(3, res);
                if (res.data.success) {
                    setData(res?.data.product);
                    setLoading(false);
                } else {
                    swal(res.data.message);

                    setLoading(false);
                }
            });
    };

    const handleSubmit = () => {
        // const data = new FormData();

        // data.append("name", values.name);
        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        axios
            .get("/api/payment/iyzipay", config)
            .then((res) => {
                console.log("res", res);
                // setScriptState(res.data);
                setIyziScript(`${res.data}`);
                setIncludeScript(true);
                // if (res.data.success) {
                //     swal("İşlem Tamamlandı");
                //     resetForm({});
                // } else {
                //     swal(res.data.message);
                // }
            })
            .catch((e) => console.log(e));
    };

    return (
        <Layout title={data?.name} description={data?.text}>
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
                    <List>
                        <ListItem>
                            <Typography component="h1" variant="h1">
                                {data?.name}
                            </Typography>
                        </ListItem>
                        {/* <ListItem>
                <Typography>Category: {data?.category}</Typography>
              </ListItem> */}
                        <ListItem>
                            <Typography>Marka: {data?.brand}</Typography>
                        </ListItem>
                        <ListItem>
                            <Typography>
                                Rating: {data?.rating} stars ({data?.numReviews}{" "}
                                reviews)
                            </Typography>
                        </ListItem>
                        <ListItem>
                            <Typography> Açıklama: {data?.text}</Typography>
                        </ListItem>
                    </List>
                </Grid>
                <Grid item md={3} xs={12}>
                    <Card>
                        <List>
                            <ListItem>
                                <Grid container>
                                    <Grid item xs={6}>
                                        <Typography>Fiyat</Typography>
                                    </Grid>
                                    <Grid item xs={6}>
                                        <Typography>
                                            {data?.buyingPrice
                                                ? data?.buyingPrice?.toFixed(2)
                                                : " - "}{" "}
                                            ₺
                                        </Typography>
                                    </Grid>
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
                    </Card>
                    <div className="script-context">
                        {includeScript && (
                            <Card>
                                <List>
                                    <ListItem>
                                        <InjectScript script={iyziScript} />
                                    </ListItem>
                                </List>
                            </Card>
                        )}
                    </div>
                </Grid>
            </Grid>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Create));
