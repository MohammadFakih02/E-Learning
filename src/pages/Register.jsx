import React, { useState } from "react";
import Button from "../components/Button";
import Input from "../components/Input";
import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";
import { useNavigate } from "react-router-dom";
const Register = () => {
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errormessage, setErrormessage] = useState("");
  return (
    <div>
      <h1>Register</h1>
      <Input
        placeholder={"username"}
        onChange={(e) => {
          setUsername(e.target.value);
        }}
      />
      <Input
        placeholder={"email"}
        onChange={(e) => {
          setEmail(e.target.value);
        }}
      />
      <Input
        placeholder={"password"}
        onChange={(e) => {
          setPassword(e.target.value);
        }}
      />
      <Button
        text={"login"}
        onClick={async () => {
          try {
            const result = await requestApi({
              body: {
                username,
                email,
                password,
              },
              method: requestMethods.POST,
              route: "/register.php",
            });
            navigate("/");
            setErrormessage("");
            console.log(result);
          } catch (error) {
            setErrormessage(error.response.data.message);
          }
        }}
      />
      <p>{errormessage}</p>
    </div>
  );
};

export default Register;
