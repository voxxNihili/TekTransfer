import axios from "axios";
import { inject, observer } from "mobx-react";
import React, { useState, useEffect } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Bar, Line } from "react-chartjs-2";
// import { PushSpinner, BallSpinner, CircleSpinner } from "react-spinners-kit";
import { Helmet } from "react-helmet";
import { Box } from "@mui/material";
import Loading from "/assets/loading.gif";
const options = {
    scales: {
        yAxes: [
            {
                ticks: {
                    beginAtZero: true,
                },
            },
        ],
    },
};

const options2 = {
    scales: {
        yAxes: [
            {
                ticks: {
                    beginAtZero: true,
                },
            },
        ],
    },
};
const Index = (props) => {
    const [loading, setLoading] = useState(true);
    const [total, setTotal] = useState({
        customer: 0,
        product: 0,
        stock: 0,
        category: 0,
    });
    const [stock, setStock] = useState({
        available: 0,
        unavailable: 0,
    });
    const [chartStock, setChartStock] = useState([]);
    const [stockTransaction, setStockTransaction] = useState([]);
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
                setTotal(res.data.total);
                setStock(res.data.stock);
                setChartStock(res.data.chartStock);
                setStockTransaction(res.data.stockTransaction);
                setLoading(false);
            });
    }, []);

    useEffect(() => {
        if (loading)
            return (
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
            );
    }, [loading]);

    const chartStockNameArray = [];
    const chartStockQuantityArray = [];
    chartStock.map((item) => {
        chartStockNameArray.push(item.modelCode);
        chartStockQuantityArray.push(item.stock);
    });

    const transactionStockDateArray = [];
    const transactionStockCountArray = [];
    stockTransaction.map((item) => {
        transactionStockCountArray.push(item.count);
        transactionStockDateArray.push(item.date);
    });

    const data = {
        labels: chartStockNameArray,
        datasets: [
            {
                label: "# Stoktaki ??r??nler",
                data: chartStockQuantityArray,
                backgroundColor: [
                    "rgba(255, 99, 132, 0.2)",
                    "rgba(54, 162, 235, 0.2)",
                    "rgba(255, 206, 86, 0.2)",
                    "rgba(75, 192, 192, 0.2)",
                    "rgba(153, 102, 255, 0.2)",
                    "rgba(255, 159, 64, 0.2)",
                ],
                borderColor: [
                    "rgba(255, 99, 132, 1)",
                    "rgba(54, 162, 235, 1)",
                    "rgba(255, 206, 86, 1)",
                    "rgba(75, 192, 192, 1)",
                    "rgba(153, 102, 255, 1)",
                    "rgba(255, 159, 64, 1)",
                ],
                borderWidth: 1,
            },
        ],
    };

    const data2 = {
        labels: transactionStockDateArray,
        datasets: [
            {
                label: "# of Votes",
                data: transactionStockCountArray,
                fill: false,
                backgroundColor: "rgb(255, 99, 132)",
                borderColor: "rgba(255, 99, 132, 0.2)",
            },
        ],
    };

    return (
        <Layout>
            <Helmet>
                <title>Muhtek - Home</title>
            </Helmet>
            <div className="container mt-5">
                <div className="row">
                    <div className="col-md-3">
                        <div className="card-item">
                            <span>Toplam Hesaplar</span>
                            <div>
                                <span>{total.customer}</span>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-3">
                        <div className="card-item">
                            <span>Toplam ??r??n</span>
                            <div>
                                <span>{total.product}</span>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-3">
                        <div className="card-item ">
                            <span>Toplam ????lem</span>
                            <div>
                                <span>{total.stock}</span>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-3">
                        <div className="card-item ">
                            <span>Toplam Kategori</span>
                            <div>
                                <span>{total.category}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="row mt-5">
                    <div className="col-md-6">
                        <div className="card-item">
                            <span>Stoktaki ??r??n Say??s??</span>
                            <div>
                                <span>{stock.available}</span>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-6">
                        <div className="card-item">
                            <span>Stokta Olmayan ??r??n Say??s??</span>
                            <div>
                                <span>{stock.unavailable}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="row mt-5">
                    <div className="col-md-6">
                        <Bar data={data} options={options} />
                    </div>
                    <div className="col-md-6">
                        <Line data={data2} options={options2} />
                    </div>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
