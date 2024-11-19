import React from "react";

const Button = ({text,onClick, bgColor})=>{
    return (
        <button className="base-button" onclick={()=>onClick}>{text}</button>
    );
}
export default Button;