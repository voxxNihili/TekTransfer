import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
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

const Create = (props) => {
    const classes = useStyles();
    const { params } = props.location.state.productId;
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState([]);

    useEffect(() => {
        settingData();
    }, []);

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

    const handleSubmit = (values, { resetForm }) => {
        const data = new FormData();

        data.append("name", values.name);
        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "multipart/form-data",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };
        axios
            .post("/api/category", data, config)
            .then((res) => {
                if (res.data.success) {
                    swal("İşlem Tamamlandı");
                    resetForm({});
                } else {
                    swal(res.data.message);
                }
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
            <Grid container   spacing={1} justifyContent="space-between">
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
                                        <Typography>{ data?.buyingPrice ? (data?.buyingPrice)?.toFixed(2) : ' - '} ₺</Typography>
                                    </Grid>
                                </Grid>
                            </ListItem>
                            {/* <ListItem>
                  <Grid container>
                    <Grid item xs={6}>
                      <Typography>Stok Durumu</Typography>
                    </Grid>
                    <Grid item xs={6}>
                      <Typography>
                        {product.countInStock > 0 ? "In stock" : "Unavailable"}
                      </Typography>
                    </Grid>
                  </Grid>
                </ListItem> */}
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
                </Grid>
            </Grid>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Create));
