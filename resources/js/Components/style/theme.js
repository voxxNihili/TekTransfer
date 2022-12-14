import { createTheme, makeStyles } from "@material-ui/core";
const theme = createTheme({
    spacing: 8,
    transition:
        "color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out",
    // transitionDuration: "0.15s, 0.15s, 0.15s, 0.15s",
    // transitionTimingFunction:
    //     "ease-in-out, ease-in-out, ease-in-out, ease-in-out",
    // transitionDelay: " 0s, 0s, 0s, 0s",
    // transitionProperty: "color, background-color, border-color, box-shadow",
});

const useStyles = makeStyles({
    primaryColor: {
        color: "#234E70",
    },
    navbar: {
        // height: "100%",
        backgroundColor: "#FFFFFF",
        "& a": {
            color: "#FFFFFF",
            // marginLeft: 10,
        },
    },
    sidebarLinks:{
        "& a": {
            color: "#FFFFFF",
        },

        "& a:hover": {
            color: "#234E70",
            textDecoration: "none",
            marginLeft: 10,
            transition: theme.transitions
        },
    
        "& a:hover li": {
            backgroundColor: "#FFFFFF",
        },
        "& a:hover li, & a:hover a": {
            transition: theme.transitions,
            transitionDuration: theme.transitionDuration,
            transitionTimingFunction: theme.transitionTimingFunction,
            transitionDelay: theme.transitionDelay,
            transitionProperty: theme.transitionProperty,
        },
    },
    sidebar: {
        height: "100%",
        backgroundColor: "#234E70",
        "& a": {
            color: "#FFFFFF",
        },
    },

    navbarLinks: {
        display: "flex",
        flexFirection: "row",
        flexWrap: "nowrap",
    },
    mainButton: {
        backgroundColor: "#234E70",
        color: "#FFFFFF",
        textTransform: "initial",
        minWidth: "150",
        marginLeft: "auto",
        "&:hover": {
            backgroundColor:"rgb(24, 54, 78)"
        }
    },
    // loginButtonOnAppBar: {
    //     marginLeft: "auto"
    // }
    //tabs
    tabs: {
        root: {
            width: "100%",
            flexGrow: 1,
            color: "#3739B5",
            backgroundColor: "white",
        },
        viewButtons: {
            marginTop: theme.spacing(1),
            marginBottom: theme.spacing(1),
        },
        "& .MuiTabs-indicator": {
            backgroundColor: "#234E70",
            height: 3,
        },
        "& .MuiTab-root.Mui-selected": {
            color: "#234E70",
        },
    },

    tabButton: {
        color: "#2ca0d8",
    },
    tabsStyle: {
        display: "flex",
        justifyContent: "space-between",
        flexDirection: "column",
    },
    headerLogo: {
        position: "absolute",
        display: "flex",
        justifyContent: "center",
        alignItems:"flex-start"
    },
    brand: {
        fontWeight: "bold",
        fontSize: "1.2rem",
    },
    grow005: {
        flexGrow: "0.05",
    },
    grow01: {
        flexGrow: "0.1",
    },
    grow1: {
        flexGrow: "1",
    },
    container: {
        position: "relative",
        minHeight: "100vh",
    },
    containerWrapper: {
        minHeight: "100vh",
        display: "flex",
        flexDirection: "column",
    },
    footer: {
        marginTop: "auto",
        // position: "absolute",
        // bottom: "0",
        width: "100%",
        // height: "2.5rem",
    },
    section: {
        marginTop: 10,
        marginBottom: 10,
    },
    bold: {
        fontWeight: 600,
    },
    form: {
        maxWidth: 800,
        margin: "10px auto",
    },
    transparentBackground: {
        backgroundColor: "transparent",
    },
    error: {
        color: "#f04040",
    },
    loginError: {
        color: "#f04040",
        margin: "0 ",
        padding: "0 1rem",
        fontSize: "0.75rem",
        marginTop: "3px",
        textAlign: "left",
        fontFamily: "Arvo",
        fontWeight: "400",
        lineHeight: "1.66",
    },
});
export default useStyles;
