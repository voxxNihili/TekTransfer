import { inject, observer } from 'mobx-react';
import React,{ useEffect , useState} from 'react';
import Layout from '../../Components/Layout/homeLayout';
import DataTable from 'react-data-table-component';
import SubHeaderComponent from '../../Components/Form/SubHeaderComponent';
import ExpandedComponent from '../../Components/Form/ExpandedComponent';
import swal from 'sweetalert';
import moment from "moment";
const Index = (props) => {
    const [data,setData] = useState([]);
    const [refresh,setRefresh] = useState(false);
    const [filter,setFilter] = useState({
        filteredData:[],
        text:'',
        isFilter:false
    });

    useEffect(() => {
        axios.get(`/api/invoice`,{
            headers:{
                Authorization: 'Bearer '+ props.AuthStore.appState.user.access_token
            }
        }).then((res) => {
           setData(res.data.data);
        })
        .catch(e => console.log(e)); 
    },[refresh]);

    const filterItem = (e) => {
        const filterText = e.target.value;
        if(filterText != '')
        {
            const filteredItems = data.filter(
                (item) => (
                    item.customer_name && item.customer_name.toLowerCase().includes(filterText.toLowerCase())
                )
            );

            setFilter({
                filteredData:filteredItems,
                text:filterText,
                isFilter:true
            })
        }
        else 
        {
            setFilter({
                filteredData:[],
                text:'',
                isFilter:false
            })
        }
    };
    const deleteItem = (item) => {
        swal({
            title:'Silmek istediğine emin misin ?',
            text:'Silinince veriler geri gelmicektir',
            icon:'warning',
            buttons:true,
            dangerMode:true
        })
        .then((willDelete) => {
        })
    }
   

    return (
        <Layout>
            <div className="container">
                <div className="row">
                    <div className="col-md-12">
                        <DataTable 
                            columns={
                                [
                                    {
                                        name: 'Durum',
                                        selector:'status',
                                        sortable:true
                                    },
                                    {
                                        name: 'Response Message',
                                        selector:'response_message',
                                        sortable:true
                                    },
                                    {
                                        name: 'Tür',
                                        selector:'type',
                                        sortable:true
                                    },
                                    {
                                        name: 'Firma',
                                        selector:'company_id',
                                        sortable:true
                                    },
                                    {
                                        name: 'Müşteri',
                                        selector:'customer_name',
                                        sortable:true
                                    },
                                    {
                                        type: Date,
                                        name: "Fatura Tarihi",
                                        selector:'invoice_date',
                                        format: (row) =>
                                            moment(row.invoice_date).format("DD.MM.YYYY h:mm:ss"),
                                        sortable: true
                                    },
                                    {
                                        type: Date,
                                        name: "Oluşturulma Tarihi",
                                        selector:'created_at',
                                        format: (row) =>
                                            moment(row.created_at).format("DD.MM.YYYY h:mm:ss"),
                                        sortable: true
                                    }
                                ]
                            }
                            subHeader={true}
                            responsive={true}
                            hover={true}
                            fixedHeader
                            pagination
                            expandableRows
                            expandableRowsComponent={<ExpandedComponent/>}
                            data={(filter.isFilter) ? filter.filteredData : data}
                            subHeaderComponent={<SubHeaderComponent filter={filterItem} action ={{ class:'btn btn-success',uri:() => props.history.push('/urunler/ekle'),title:'Yeni Ürün Ekle'}} />}
                        />
                    </div>
                    {console.log(data)}
                </div>
            </div>
            
        </Layout>
    )
};
export default inject("AuthStore")(observer(Index));