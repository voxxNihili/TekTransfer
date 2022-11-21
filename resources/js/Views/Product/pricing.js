import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import { DataGrid } from "@mui/x-data-grid";
import { string } from "yup";
import Table from "@material-ui/core/Table";
import TableHead from "@material-ui/core/TableHead";
import TableRow from "@material-ui/core/TableRow";
import TableCell from "@material-ui/core/TableCell";
import TableBody from "@material-ui/core/TableBody";
import TablePagination from "@material-ui/core/TablePagination";
import Paper from "@mui/material/Paper";
import { replace } from "lodash";
import Input from "@mui/material/Input";
import swal from "sweetalert";
import Layout from "../../Components/Layout/homeLayout";
import { Box, TableContainer } from "@mui/material";
import { Button, TextField } from "@material-ui/core";
import { Form } from "react-bootstrap";
import Loading from "/assets/loadingAlt.gif";
 

const BasicEditingGrid = (props) => {
    const [loading, setLoading] = useState(true);
    const [colsLoaded, setColsLoaded] = useState(false);
    const [cols, setCols] = useState([]);
    const [rows, setRows] = useState([]);
    const [price, setPrice] = useState([]);
    useEffect(() => {
        axios
            .get(`/api/productUserNumber`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                let items = res.data.data;
                items = items.map((item) => {
                    return {
                        id: item.id,
                        field: String(item.number),
                        headerName: String(item.number) + " Kullanıcı",
                        width: 150,
                        editable: true,
                    };
                });
                setCols(items);
                setColsLoaded(true);
            })
            .catch((e) => swal(res.data.message));
    }, []);

    useEffect(() => {
        setLoading(true);
        axios
            .get(`/api/productMonthNumber`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                let rowItems = res.data.data;
                setRows(rowItems);
            })
            .catch((e) => swal(res.data.message));

        axios
            .get(`/api/price/` + props.match.params.id, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                let prices = res.data.data;
                setPrice(prices);
                setLoading(false);
            })
            .catch((e) => swal(res.data.message));
    }, [colsLoaded]);

    const handleSubmit = (e) => {
        setLoading(true);
        e.preventDefault();
        if (rows.length * cols.length == price.length) {
            var params = {
                productId: props.match.params.id,
                prices: price,
            };
            axios
                .post(`/api/price`, params, {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                })
                .then((res) => {
                    swal(res.data.message);
                })
                .catch((res) => swal(res.data.message));
        } else {
            swal("Eksik Alanları Doldurunuz!");
        }

        setLoading(false);
    };

    const onChangeInput = (e) => {
        let value = {
            monthLimitId: Number(e.target.attributes.monthkey.value),
            userLimitId: Number(e.target.attributes.userkey.value),
            price: e.target.value,
        };

        if (
            price?.find(
                (i) =>
                    i.monthLimitId === value.monthLimitId &&
                    i.userLimitId === value.userLimitId
            )
        ) {
            const newState = price?.map((obj) => {
                if (
                    obj.monthLimitId === value.monthLimitId &&
                    obj.userLimitId === value.userLimitId
                ) {
                    return {
                        ...obj,
                        price: replace(value.price ? value.price : 0, ",", "."),
                    };
                }
                return obj;
            });
            setPrice(newState);
        } else {
            setPrice((current) => [...current, value]);
        }
    };

    return (
        <Layout>
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
                <div className={"col-md-12 d-flex align-items-center"}>
                    <form onSubmit={handleSubmit}>
                        <TableContainer component={Paper}>
                            <Table>
                                <TableHead align="right">
                                    <TableRow>
                                        <TableCell>Aylar</TableCell>
                                        {cols.map((column,idx) => (
                                            <TableCell key={idx}>
                                                {column.headerName}
                                            </TableCell>
                                        ))}
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {rows.map((row, idx) => (
                                        <TableRow key={idx}>
                                            <TableCell>
                                                {row.number === 0
                                                    ? "Deneme"
                                                    : row.number + " Ay"}
                                            </TableCell>
                                            {cols.map((column2,idy) => (
                                                <TableCell align="right" key={idy}>
                                                    <Input
                                                        onChange={(e) =>
                                                            onChangeInput(e)
                                                        }
                                                        inputProps={{
                                                            monthkey: row.id,
                                                            userkey: column2.id,
                                                        }}
                                                        value={
                                                            price?.find(
                                                                (i) =>
                                                                    i.monthLimitId ===
                                                                        row.id &&
                                                                    i.userLimitId ===
                                                                        column2.id
                                                            )?.price
                                                        }
                                                        type={"number"}
                                                    ></Input>
                                                </TableCell>
                                            ))}
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </TableContainer>
                        <Button
                            variant="contained"
                            type="submit"
                            color="primary"
                        >
                            Kaydet
                        </Button>
                    </form>
                </div>
            )}
        </Layout>
    );
};
export default inject("AuthStore")(observer(BasicEditingGrid));
