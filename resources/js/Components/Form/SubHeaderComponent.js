import React from 'react';
const SubHeaderComponent = (props) => {
    return (
        <div style={{ display:'flex'}}>
            {props.action && <button className={props.action?.class} onClick={props.action?.uri}>
                {props.action?.title}
            </button>}
            {!props.inputDestroyer && <input placeholder={"ara"} onChange={props.filter} style={{ flex:1}} type="text"
            className="ml-1 form-control" />}
        </div>
    )
};
export default SubHeaderComponent;