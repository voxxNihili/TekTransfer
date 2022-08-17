// import React, { useState, useEffect } from "react";
// // import { Link } from "react-router-dom";
// // import { Formik } from "formik";
// // import * as Yup from "yup";
// import axios from "axios";
// import { inject, observer } from "mobx-react";
// // import HomeLayout from "../../Components/Layout/homeLayout";
// import { CircleSpinner } from "react-spinners-kit";
// // import ProductCards from "../../Components/Utils/ProductCard"
// // import NavMenu from "../../Components/Utils/Tabs"

// const LandingPageProducts = (props) => {
//     const [loading, setLoading] = useState(true);
//     const [refresh,setRefresh] = useState(false);
//     const [data,setData] = useState(false);
//     useEffect(() => {
//         axios.get(`/api/product`,{
//             headers:{
//                 Authorization: 'Bearer '+ props.AuthStore.appState.user.access_token
//             }
//         }).then((res) => {
//            setData(res.data.data);
//         })
//         .catch(e => console.log(e)); 
//     },[refresh]);
    
//     useEffect(() => {
//         if (loading)
//             return (
//                 <div className="loading-a">
//                     <CircleSpinner size={50} color="#686769" loading={true} />
//                 </div>
//             );
//     },[loading]);

   
    
//     return (
//          <div></div>
//     );
// };
// export default inject("AuthStore")(observer(LandingPageProducts));
