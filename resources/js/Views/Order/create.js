import { inject, observer } from 'mobx-react';
import React,{ useEffect,useState} from 'react';
import Layout from '../../Components/Layout/front.layout';
import { Formik } from 'formik';
import * as Yup from 'yup';
import CustomInput from '../../Components/Form/CustomInput';
import Select from 'react-select';
import ImageUploader from 'react-images-upload';
import CKEditor from 'ckeditor4-react';
import swal from 'sweetalert';
import axios from 'axios';
import Card from 'react-bootstrap/Card';
import Button from 'react-bootstrap/Button';
const Create = (props) => {
  
    const {params} = props.location.state.productId;
    const [loading,setLoading] = useState(true);
    const [data,setData] = useState([]);
    
    useEffect(() => {
        settingData();
    },[]);

    const settingData = async () =>{
        await axios.get(`/api/product/${props.location.state.productId} `,{
            headers:{
                Authorization: 'Bearer '+ props.AuthStore.appState.user.access_token
            }
        }).then((res) => {
            console.log(3,res);
            if(res.data.success){
                setData(res?.data.product);
                setLoading(false);          
            }
            else    
            {
                swal(res.data.message);

                setLoading(false);         
            }   
            
        })               
    }

    const handleSubmit = (values,{ resetForm }) => {
        const data = new FormData();

        data.append('name',values.name);
        const config = {
            headers:{
                'Accept':'application/json',
                'content-type':'multipart/form-data',
                'Authorization':'Bearer '+ props.AuthStore.appState.user.access_token
            }
        }
        axios.post('/api/category',data,config)
        .then((res) => {
            if(res.data.success){
                swal("İşlem Tamamlandı");
                resetForm({});
            }
            else 
            {
                swal(res.data.message);
            }
        })
        .catch(e => console.log(e));

    };

  
    return (
        <Layout>
        
            {console.log("içerdeki baştaki data", data)}
            <div className="mt-5">
            <div className="container">
            <Formik 
            initialValues={{
              name:'',
            }}
            onSubmit={handleSubmit}
            validationSchema={
              Yup.object().shape({
               name:Yup.string().required('Kategori Adı Zorunludur'),
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
                <div className="card">
                <div className="row no-gutters">
                    <div className="col-auto">
                        <img src={data?.images?.[0]?.path} className="img-fluid" alt=""></img>
                    </div>
                    <div className="col">
                        <div className="card-block px-2">
                            <h4 className="card-title text-center">{data?.name ? data?.name : ' - '}</h4>
                            <div className="card-text">
                                <div><span>Ürün Adı : </span><span>{data?.name ? data?.name : ' - '}</span></div>
                                <div><span>Marka : </span><span>{data?.brand ? data?.brand : ' - '}</span></div>
                                <div><span>Açıklama : </span><span>{data?.text ? data?.text : ' - '}</span></div>
                                <div><span>Fiyat : </span><span>{ data?.buyingPrice ? (data?.buyingPrice)?.toFixed(2) : ' - '} ₺</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div className="card-footer w-100 text-muted">
                    <button 
                        disabled={!isValid || isSubmitting}
                        onClick={handleSubmit}
                        className="btn btn-lg btn-primary btn-block" 
                        type="button">
                        Satın Al
                    </button>
                    </div>
                </div>
              </div>
              )}
          </Formik>
          </div>
          </div>
        </Layout>
    )
};
export default inject("AuthStore")(observer(Create));