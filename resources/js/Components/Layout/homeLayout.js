import React,{ useState,useEffect} from 'react';
import axios from 'axios';
import { inject, observer } from 'mobx-react';
import { useHistory ,Link} from 'react-router-dom';
import { Navbar , Nav , NavDropdown , Container , Button  ,Form} from 'react-bootstrap';
import { LinkContainer } from 'react-router-bootstrap';
const HomeLayout = (props) => {
    const [user,setUser] = useState({});
    const [register,setRegister] = useState(false);
    const [isLoggedIn,setIsLoggedIn] = useState(false);
    props.AuthStore.getToken();
    const history = useHistory();

    useEffect(() => {
        const token = (props.AuthStore.appState != null) ? props.AuthStore.appState.user.access_token : null;
        axios.post(`/api/authenticate`,{},{
            headers:{
                Authorization: 'Bearer '+ token
            }
        }).then((res) => {
            if(!res.data.isLoggedIn){
                setRegister = false;
            }
            setUser(res.data.user);
            setIsLoggedIn(res.data.isLoggedIn);
        })
        .catch(e => {
            setRegister = false;
        }); 
        
    },[])

    const logout = () => {
        
        axios.post(`/api/logout`,{},{
            headers:{
                Authorization: 'Bearer '+ props.AuthStore.appState.user.access_token
            }
        }).then(res => console.log(res)).catch(e => console.log(e));
        props.AuthStore.removeToken();
        history.push('/login');
    }
    return (
        <>
        <Navbar  collapseOnSelect expand="lg" bg="dark" variant="dark">
            <Container>
            <LinkContainer to="/">
                <Navbar.Brand >Muhtek</Navbar.Brand>
            </LinkContainer>
            <Navbar.Toggle aria-controls="basic-navbar-nav" />
            <Navbar.Collapse id="basic-navbar-nav">
                <Nav className="mr-auto">
                    <LinkContainer to="/kategoriler" style={{display: register ? "block" : "none"}}>
                        <Nav.Link >Kategoriler</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to="/urunler" style={{display: register ? "block" : "none"}}>
                        <Nav.Link >Ürünler</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to="/siparisler" style={{display: register ? "block" : "none"}}>
                        <Nav.Link >Siparişler</Nav.Link>
                    </LinkContainer>
                    {/* <LinkContainer to="/stok">
                        <Nav.Link>Stok</Nav.Link>
                    </LinkContainer> */}
                </Nav>
                <Nav>
                
                <NavDropdown title={register ? user.name : "Giriş"} id="basic-nav-dropdown" >
                    <LinkContainer to="/login" style={{display: register ? "none" : "block"}}>
                        <NavDropdown.Item >Giriş Yap</NavDropdown.Item>
                    </LinkContainer>
                    <LinkContainer to="/register" style={{display: register ? "none" : "block"}}>
                        <NavDropdown.Item >Kayıt Ol</NavDropdown.Item>
                    </LinkContainer>
                    <LinkContainer to="/profil" style={{display: register ? "block" : "none"}}>
                        <NavDropdown.Item >Profil Düzenle</NavDropdown.Item>
                    </LinkContainer>
                    <NavDropdown.Divider />
                    <NavDropdown.Item onClick={logout} style={{display: register ? "block" : "none"}}>Çıkış</NavDropdown.Item>
                </NavDropdown>
                 
                </Nav>
            </Navbar.Collapse>
            </Container>
        </Navbar>
            <div>{props.children}</div>
        </>
    )
}

export default inject("AuthStore")(observer(HomeLayout));