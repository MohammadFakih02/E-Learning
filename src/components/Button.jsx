import React from "react";
import "../../styles/Button.css";

const Button = ({ text, onClick, bgColor }) => {
  return (
    <button className="base-button" onClick={() => onClick()}>
      {text}
    </button>
  );
};

export default Button;
