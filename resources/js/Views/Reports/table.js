import { useEffect } from "react";

function DynamicTable(props){
 const column = Object.keys(props.dataTable[0]);
 const ThData =()=>{
    
     return column.map((data)=>{
         return <th key={data}>{data}</th>
     })
 }
// get table row data
const tdData =() =>{
   
     return props.dataTable.map((data)=>{
       return(
           <tr>
                {
                   column.map((v)=>{
                       return <td>{data[v]}</td>
                   })
                }
           </tr>
       )
     })
}
  return (
      <table className="table">
        <thead>
         <tr>{ThData()}</tr>
        </thead>
        <tbody>
        {tdData()}
        </tbody>
       </table>
  )
}
export default DynamicTable;