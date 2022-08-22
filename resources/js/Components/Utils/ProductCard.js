import React, { useEffect, useState } from "react";
import { makeStyles } from "@material-ui/core/styles";
import Card from "@material-ui/core/Card";
import CardActionArea from "@material-ui/core/CardActionArea";
import CardActions from "@material-ui/core/CardActions";
import CardContent from "@material-ui/core/CardContent";
import CardMedia from "@material-ui/core/CardMedia";
import Button from "@material-ui/core/Button";
import Typography from "@material-ui/core/Typography";
import IconButton from "@material-ui/core/IconButton";
import { red } from "@material-ui/core/colors";
import FavoriteIcon from "@material-ui/icons/Favorite";
import ShareIcon from "@material-ui/icons/Share";
import { Grid } from "@material-ui/core";
import useStyles from "../style/theme";
import { inject, observer } from "mobx-react";
function ProductCard(props) {
    const classes = useStyles();
    const [data, setData] = useState([]);
    const handleSubmit = () => {
        axios
            .post(`/api/order`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
                productId: props.productId,
                userId: props.userId,
            })
            .then((res) => {
                setData(res);
                console.log("resdata", res);
            })
            .catch((e) => console.log(e));

    };

    // useEffect(() => {

    // },[]);
    return (
        <Grid container style={{ display: "grid", height: "100%" }}>
            <Grid item style={{ display: "flex" }}>
                <Card className={classes.tabsStyle}>
                    <CardContent>
                        <Typography gutterBottom variant="h5" component="h2">
                            {props.cardName}
                        </Typography>
                        <Typography
                            variant="body2"
                            color="textSecondary"
                            component="p"
                        >
                            CardActions are just a flexbox component that wraps
                            the children in 8px of padding and 8px horizontal
                            padding between children.
                        </Typography>
                    </CardContent>
                    <CardActions disableSpacing>
                        <Button
                            size="small"
                            className={classes.tabButton}
                            color="primary"
                        >
                            İncele
                        </Button>
                        <Button
                            size="small"
                            onClick={handleSubmit}
                            className={classes.tabButton}
                            color="primary"
                        >
                            Satın Al
                        </Button>
                    </CardActions>
                </Card>
            </Grid>
        </Grid>
    );
}
export default inject("AuthStore")(observer(ProductCard));
