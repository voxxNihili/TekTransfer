const Dropdown = ({ label, value, options, onChange }) => {
    return (
        <div className="form-group">
            <label>
                {label && label} </label>
                <select
                    className="form-control"
                    value={value}
                    onChange={onChange}
                >
                    {options.length > 0 && options.map((option) => (
                        <option key={option.key} data-tag={option.dataTag} value={option.value}>{option.label}</option>
                    ))}
                    
                </select>
           
        </div>
    );
};
export default Dropdown;
