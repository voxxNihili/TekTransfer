import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { Formik } from "formik";
import DataTable from "react-data-table-component";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Select from "react-select";
import ImageUploader from "react-images-upload";
import CKEditor from "ckeditor4-react";
import swal from "sweetalert";
import axios from "axios";
import { difference } from "lodash";
import { Row } from "react-bootstrap";
const Edit = (props) => {
    const { params } = props.match;
    const [rowsSelected, setRowsSelected] = useState([]);
    const [rowsInitiallySelected, setRowsInitiallySelected] = useState([]);
    const [loading, setLoading] = useState(true);
    const [queries, setQueries] = useState([]);
    const [data, setData] = useState([]);
    const [refresh, setRefresh] = useState(false);
    useEffect(() => {
        axios
            .get(`/api/queryParameter`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                setData(res.data.data);
            })
            .catch((e) => console.log(e));
    }, [refresh]);

    useEffect(() => {
        axios
            .get(`/api/query/${params.id} `, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    setQueries(res.data.query);
                    setLoading(false);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((e) => console.log(e));
    }, []);

    useEffect(() => {
        queries?.query_param?.map((item) => {
            setRowsInitiallySelected((oldArray) => [
                item.parameter_id,
                ...oldArray,
            ]);
        });
    }, [queries]);

    const handleChange = ({ selectedRows }) => {
        const arrayOfSelections = Object.values(selectedRows);
        setRowsSelected(arrayOfSelections);
    };
    const handleSubmit = (values, { resetForm, setSubmitting }) => {
        console.log(1111,values);
        console.log(1111,rowsSelected);
        
        let params = {
            name: values.name,
            code: values.code,
            sqlQuery: values.sqlQuery,
            selectedRows: rowsSelected,
        };

        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "application/json",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };

        axios
            .put(`/api/query/${queries.id}`, params, config)
            .then((res) => {
                if (res.data.success) {
                    swal("Sorgu Düzenlendi");
                    setRefresh(!refresh);
                    resetForm({});
                    setSubmitting(false);
                } else {
                    swal(res.data.message);
                    setSubmitting(true);
                }
            })
            .catch((e) => {
                setSubmitting(true);
                console.log(e);
            });
    };

    if (loading) return <div>Yükleniyor</div>;
    // const rowSelectCriteria = row => row.id>6;


    
    function selected(data, states, column1, column2) {
      return data.map(i => {
        states.forEach(state => {
          if (i[column1] === state[column2]) {
            i["state"] = 1;
          }
        })
        return i;
      })
    }
        
    selected(data, queries.query_param, "id", "parameter_id");



    const rowSelectCriteria = (row) => row.state === 1;

    console.log("rowSelectCriteria", rowSelectCriteria);

    // function rowSelectCritera(row) {
    //     rowsInitiallySelected?.map((item) => {
    //          return( row.id >6 )
    //     });
    // }
    // console.log("rowSelectCriteria", rowSelectCriteria);

    return (
        <Layout>
            {console.log("queries", queries.query_param)}
            <div className="mt-5">
                <div className="row container">
                    <div className="col-md-12">
                        <Formik
                            initialValues={{
                                name: queries.name,
                                code: queries.code,
                                sqlQuery: queries.sqlQuery,
                            }}
                            onSubmit={handleSubmit}
                            validationSchema={Yup.object().shape({
                                name: Yup.string().required(
                                    "Sorgu Adı Zorunludur"
                                ),
                                code: Yup.string().required(
                                    "Kısa Kod Zorunludur"
                                ),
                                sqlQuery:
                                    Yup.string().required("Sorgu Zorunludur"),
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
                                            <CustomInput
                                                title="Sorgu Adı"
                                                value={values.name}
                                                handleChange={handleChange(
                                                    "name"
                                                )}
                                            />
                                            {errors.name && touched.name && (
                                                <p className="form-error">
                                                    {errors.name}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-md-6">
                                            <CustomInput
                                                title="Kısa Kod"
                                                value={values.code}
                                                handleChange={handleChange(
                                                    "code"
                                                )}
                                            />
                                            {errors.code && touched.code && (
                                                <p className="form-error">
                                                    {errors.code}
                                                </p>
                                            )}
                                        </div>
                                        <div className="col-md-6">
                                            <CustomInput
                                                title="Sorgu"
                                                value={values.sqlQuery}
                                                handleChange={handleChange(
                                                    "sqlQuery"
                                                )}
                                            />
                                            {errors.sqlQuery &&
                                                touched.sqlQuery && (
                                                    <p className="form-error">
                                                        {errors.sqlQuery}
                                                    </p>
                                                )}
                                        </div>
                                    </div>
                                    <button
                                        disabled={!isValid || isSubmitting}
                                        onClick={handleSubmit}
                                        className="btn btn-lg btn-primary btn-block"
                                        type="button"
                                    >
                                        Sorguyu Düzenle
                                    </button>
                                </div>
                            )}
                        </Formik>
                    </div>
                    <div className="col-md-12 mt-5">
                        <DataTable
                            columns={[
                                {
                                    name: "PARAMETRE",
                                    selector: "parameter",
                                    width: "auto",
                                },
                                {
                                    name: "PARAMETRE ADI",
                                    selector: "name",
                                    width: "auto",
                                },
                                {
                                    name: "PARAMETRE TÜRÜ",
                                    selector: "data_type",
                                    width: "auto",
                                },
                            ]}
                            width="auto"
                            maxWidth={"auto"}
                            subHeader={true}
                            responsive={true}
                            // hover={true}
                            // onRowClicked={clickHandler}
                            // fixedHeader
                            selectableRows
                            onSelectedRowsChange={handleChange}
                            selectableRowSelected={rowSelectCriteria}
                            // clearSelectedRows={toggledClearRows}
                            // expandableRows
                            // expandableRowsComponent={<ExpandedComponent />}
                            data={data}
                            subHeaderComponent={
                                <div
                                    className="row"
                                    style={{
                                        textAlign: "start",
                                        margin: "auto",
                                    }}
                                >
                                    Parametreler
                                </div>
                            }
                        />
                    </div>
                </div>
            </div>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Edit));
