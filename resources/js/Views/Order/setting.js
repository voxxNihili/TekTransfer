import { inject, observer } from 'mobx-react';
import React,{ useEffect,useState} from 'react';
import Layout from '../../Components/Layout/homeLayout';
import { Formik } from 'formik';
import * as Yup from 'yup';
import CustomInput from '../../Components/Form/CustomInput';
import Select from 'react-select';
import ImageUploader from 'react-images-upload';
import CKEditor from 'ckeditor4-react';
import swal from 'sweetalert';
import axios from 'axios';
import { Redirect } from 'react-router-dom';

const Setting = (props) => {

    const {params} = props.match;
    const [loading,setLoading] = useState(true);
    const [data,setData] = useState([]);

    useEffect(() => {
        settingData();

    },[]);

    const settingData = async () =>{
        await axios.get(`/api/order/setting/${params.id} `,{
            headers:{
                Authorization: 'Bearer '+ props.AuthStore.appState.user.access_token
            }
        }).then((res) => {
            console.log(3,res);
            if(res.data.success){
                setData(res?.data.setting);
                setLoading(false);
            }
            else
            {
                swal(res.data.message);

                setLoading(false);
            }

        })
    }

    const goToLoginPage = () => window.location.href = '/siparisler';

    const handleSubmit = (values,{ resetForm }) => {
        const data = new FormData();
        data.append('customerCode',values.customerCode);
        data.append('customerType',values.customerType);
        data.append('companyId',values.companyId);
        data.append('companyName',values.companyName);
        data.append('orderId',props.match.params.id);
        data.append('sqlPeriod',values.sqlPeriod);
        data.append('sqlCompanyId',values.sqlCompanyId);
        const config = {
            headers:{
                'Accept':'application/json',
                'content-type':'multipart/form-data',
                'Authorization':'Bearer '+ props.AuthStore.appState.user.access_token
            }
        }
        axios.post('/api/order/setting',data,config)
        .then((res) => {
            if(res.data.success){
                goToLoginPage();
            }
            else
            {
                swal(res.data.message);
            }
        })
        .catch(e => console.log(e));

    };
    if(loading) return <div>Yükleniyor</div>

    return (
        <Layout>
            <div className="mt-5">
            <div className="container">
            <Formik
            initialValues={{
              customerCode:data?.customerCode,
              customerType:data?.customerType,
              companyId:data?.companyId,
              companyName:data?.companyName,
              sqlPeriod:data?.sqlPeriod,
              sqlCompanyId:data?.sqlCompanyId
            }}
            onSubmit={handleSubmit}
            validationSchema={
              Yup.object().shape({
               customerCode:Yup.string().required('Müşteri Cari Kodu Adı Zorunludur'),
               customerType:Yup.number().required('Müşteri Cari Türü Zorunludur'),
               companyId:Yup.number().required('Firma Idsi Zorunludur'),
               companyName:Yup.string().required('Firma Adı Zorunludur'),
               sqlPeriod:Yup.string().required('Logo Dönem Zorunludur'),
               sqlCompanyId:Yup.string().required('Logo Sql Firma Zorunludur'),
              })
            }
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
                touched
              }) => (
              <div>

                <div className="row">
                    <div className="col-md-12">
                        <CustomInput
                            title="Müşteri Cari Kodu"
                            value={values.customerCode}
                            handleChange={handleChange('customerCode')}
                        />
                        {(errors.customerCode && touched.customerCode) && <p className="form-error">{errors.customerCode}</p>}
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12">
                        <CustomInput
                            title="Müşteri Cari Türü"
                            value={values.customerType}
                            type="number"
                            handleChange={handleChange('customerType')}
                        />
                        {(errors.customerType && touched.customerType) && <p className="form-error">{errors.customerType}</p>}
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12">
                        <CustomInput
                            title="Firma Id"
                            value={values.companyId}
                            type="number"
                            handleChange={handleChange('companyId')}
                        />
                        {(errors.companyId && touched.companyId) && <p className="form-error">{errors.companyId}</p>}
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12">
                        <CustomInput
                            title="Firma Adı"
                            value={values.companyName}
                            handleChange={handleChange('companyName')}
                        />
                        {(errors.companyName && touched.companyName) && <p className="form-error">{errors.companyName}</p>}
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12">
                        <CustomInput
                            title="Logo Dönem (LG_XXX_??)"
                            value={values.sqlPeriod}
                            type="number"
                            handleChange={handleChange('sqlPeriod')}
                        />
                        {(errors.sqlPeriod && touched.sqlPeriod) && <p className="form-error">{errors.sqlPeriod}</p>}
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12">
                        <CustomInput
                            title="Logo Sql Firma (LG_???_XX)"
                            value={values.sqlCompanyId}
                            type="number"
                            handleChange={handleChange('sqlCompanyId')}
                        />
                        {(errors.sqlCompanyId && touched.sqlCompanyId) && <p className="form-error">{errors.sqlCompanyId}</p>}
                    </div>
                </div>
                <button
                disabled={!isValid || isSubmitting}
                onClick={handleSubmit}
                className="btn btn-lg btn-primary btn-block"
                type="button">
                Ayarı Kaydet
                </button>
            </div>
              )}
          </Formik>
          </div>
          </div>
        </Layout>
    )
};
export default inject("AuthStore")(observer(Setting));