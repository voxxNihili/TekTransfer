import React from 'react';
import { Route , Switch} from 'react-router-dom';
import PrivateRoute from './PrivateRoute';
/* Sayfalar */


import FrontHome from './Views/Index/index';

import Admin from './Views/Admin/index';
import FrontLogin from './Views/Login/index';
import FrontRegister from './Views/Register';
import FrontForgetPassword from './Views/ForgetPassword';
/* Ürünler */
import ProductIndex from './Views/Product/index';
import ProductCreate from './Views/Product/create';
import ProductEdit from './Views/Product/edit';
import ProductPricing from './Views/Product/pricing';

/* Sorgu Parametreleri */
import QueryParametersIndex from './Views/QueryParameters/index';
import QueryParametersCreate from './Views/QueryParameters/create';
import QueryParametersEdit from './Views/QueryParameters/edit';

/* Sorgu */
import QueryIndex from './Views/Query/index';
import QueryCreate from './Views/Query/create';
import QueryEdit from './Views/Query/edit';


/* Kategoriler */
import CategoryIndex from './Views/Category/index';
import CategoryCreate from './Views/Category/create';
import CategoryEdit from './Views/Category/edit';

/* Müşteriler */
import CustomerIndex from './Views/Customer/index';
import CustomerCreate from './Views/Customer/create';
import CustomerEdit from './Views/Customer/edit';
/* Siparişler */
import OrderIndex from './Views/Order/index';
import OrderCreate from './Views/Order/create';
//import OrderEdit from './Views/Order/edit';
import OrderSetting from './Views/Order/setting';
import SelectCompany from './Views/Order/select-companies';

// Raporlar
import ReportsIndex from './Views/Reports/index';
import ReportIndex from './Views/Reports/reportIndex';


/* Stok */
import StockIndex from './Views/Stock/index';
import StockCreate from './Views/Stock/create';
import StockEdit from './Views/Stock/edit';
/* Profil */
import ProfileIndex from './Views/Profile/index';



const Main = () => (
    <Switch>

        <Route exact path="/" component={FrontHome} />

        <PrivateRoute  path="/admin" component={Admin} />
        <Route path="/login" component={FrontLogin} />
        <Route path="/register" component={FrontRegister} />
        <Route path="/forgetpassword" component={FrontForgetPassword} />

        <PrivateRoute exact path="/urunler" component={ProductIndex} />
        <PrivateRoute  path="/urunler/ekle" component={ProductCreate} />
        <PrivateRoute  path="/urunler/duzenle/:id" component={ProductEdit} />
        <PrivateRoute  path="/urunler/fiyatlandir/:id" component={ProductPricing} />

        <PrivateRoute exact path="/sorgu-parametreleri" component={QueryParametersIndex} />
        <PrivateRoute  path="/sorgu-parametreleri/ekle" component={QueryParametersCreate} />
        <PrivateRoute  path="/sorgu-parametreleri/duzenle/:id" component={QueryParametersEdit} />

        <PrivateRoute exact path="/sorgular" component={QueryIndex} />
        <PrivateRoute  path="/sorgular/ekle" component={QueryCreate} />
        <PrivateRoute  path="/sorgular/duzenle/:id" component={QueryEdit} />

        <PrivateRoute exact path="/kategoriler" component={CategoryIndex} />
        <PrivateRoute  path="/kategori/ekle" component={CategoryCreate} />
        <PrivateRoute  path="/kategori/duzenle/:id" component={CategoryEdit} />

        <PrivateRoute exact path="/musteriler" component={CustomerIndex} />
        <PrivateRoute  path="/musteri/ekle" component={CustomerCreate} />
        <PrivateRoute  path="/musteri/duzenle/:id" component={CustomerEdit} />

        <PrivateRoute exact path="/siparisler" component={OrderIndex} />
        <PrivateRoute  path="/siparis/ekle" component={OrderCreate} />
        {/*<PrivateRoute  path="/siparis/duzenle/:id" component={OrderEdit} /> */}
        <PrivateRoute exact path="/siparisler/ayar/:id" component={OrderSetting} />
        <PrivateRoute exact path="/siparisler/firma-sec" component={SelectCompany} />
        

        <PrivateRoute exact path="/raporlar" component={ReportsIndex} />
        <PrivateRoute  path="/raporlar/:id" component={ReportIndex} />



        <PrivateRoute exact path="/stok" component={StockIndex} />
        <PrivateRoute  path="/stok/ekle" component={StockCreate} />
        <PrivateRoute  path="/stok/duzenle/:id" component={StockEdit} />

        <PrivateRoute  path="/profil" component={ProfileIndex} />

    </Switch>
);
export default Main;
