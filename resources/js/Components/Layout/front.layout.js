import React, { useState, useEffect } from "react";
import axios from "axios";
import { inject, observer } from "mobx-react";
import { useHistory, Link } from "react-router-dom";
import {
    Navbar,
    Nav,
    NavDropdown,
    Container,
    Button,
    Form,
} from "react-bootstrap";
import { LinkContainer } from "react-router-bootstrap";
const Layout = (props) => {
    const [user, setUser] = useState({});
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    props.AuthStore.getToken();
    const history = useHistory();

    useEffect(() => {
        const token =
            props.AuthStore.appState != null
                ? props.AuthStore.appState.user.access_token
                : null;
        axios
            .post(
                `/api/authenticate`,
                {},
                {
                    headers: {
                        Authorization: "Bearer " + token,
                    },
                }
            )
            .then((res) => {
                if (!res.data.isLoggedIn) {
                    history.push("/login");
                }
                setUser(res.data.user);
                setIsLoggedIn(res.data.isLoggedIn);
            })
            .catch((e) => {
                history.push("/login");
            });
    }, []);

    const logout = () => {
        axios
            .post(
                `/api/logout`,
                {},
                {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                }
            )
            .then((res) => console.log(res))
            .catch((e) => console.log(e));
        props.AuthStore.removeToken();
        history.push("/login");
    };
    return (
        <>
            <Navbar collapseOnSelect expand="lg" bg="dark" variant="dark">
                <Container>
                    <LinkContainer to="/">
                        <Navbar.Brand>Muhtek</Navbar.Brand>
                    </LinkContainer>
                    <Navbar.Toggle aria-controls="basic-navbar-nav" />
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="mr-auto">
                            <LinkContainer to="/admin">
                                <Nav.Link>Y??netim Paneli</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/musteriler">
                                <Nav.Link>M????teri & Tedarik??i</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/kategoriler">
                                <Nav.Link>Kategoriler</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/urunler">
                                <Nav.Link>??r??nler</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/sorgu-parametreleri">
                                <Nav.Link>Sorgular</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/sorgular">
                                <Nav.Link>Sorgular</Nav.Link>
                            </LinkContainer>

                            <LinkContainer to="/siparisler">
                                <Nav.Link>Sipari??ler</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/raporlar">
                                <Nav.Link>Raporlar</Nav.Link>
                            </LinkContainer>
                            <LinkContainer to="/faturalar">
                                <Nav.Link>Faturalar</Nav.Link>
                            </LinkContainer>
                            {/* <LinkContainer to="/stok">
                        <Nav.Link>Stok</Nav.Link>
                    </LinkContainer> */}
                        </Nav>
                        <Nav>
                            <NavDropdown
                                title={user.name}
                                id="basic-nav-dropdown"
                            >
                                <LinkContainer to="/profil">
                                    <NavDropdown.Item>
                                        Profil D??zenle
                                    </NavDropdown.Item>
                                </LinkContainer>

                                <NavDropdown.Divider />
                                <NavDropdown.Item onClick={logout}>
                                    ????k????
                                </NavDropdown.Item>
                            </NavDropdown>
                        </Nav>
                    </Navbar.Collapse>
                </Container>
            </Navbar>
            <div>{props.children}</div>
        </>
    );
};

export default inject("AuthStore")(observer(Layout));
