import { createTheme, makeStyles } from "@material-ui/core";
const theme = createTheme({
    spacing: 8,
});
const useStyles = makeStyles({
    primaryColor: {
        color: "#234E70",
    },
    navbar: {
        backgroundColor: "#234E70",
        "& a": {
            color: "#FFFFFF",
            marginLeft: 10,
        },
    },
    navbarLinks: {
        display: "flex",
        flexFirection: "row",
        flexWrap: "nowrap",
    },
    mainButton: {
        backgroundColor: "#2ca0d8",
        color: "#FFFFFF",
        textTransform: "initial",
        minWidth: "150",
    },
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
    main: {
        minHeight: "80vh",
    },
    footer: {
        marginTop: 10,
        textAlign: "center",
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
});
export default useStyles;
