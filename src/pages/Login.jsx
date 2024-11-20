import React, { useState } from "react";
import Button from "../components/Button";
import Input from "../components/Input";
import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";
import { useNavigate } from "react-router-dom";
import { jwtDecode } from 'jwt-decode';
const Login = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errormessage, setErrormessage] = useState("");
  return (
    <div>
      <h1>Login</h1>
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
                email,
                password,
              },
              method: requestMethods.POST,
              route: "/login.php",
            });
            localStorage.setItem("token", result.token);
            const decoded = jwtDecode(result.token);
            const role = decoded.role;
            console.log(role);
            if (role === "admin") {
              navigate("/admin");
            } else if (role === "instructor") {
              navigate("/instructor");
            } else {
              navigate("/Courses");
            }

            setErrormessage("");
            console.log(result);
          } catch (error) {
            setErrormessage(error.response.data.message);
          }
        }}
      />
      <p>{errormessage}</p>
      <p className="link"
        onClick={() => {
          navigate("/register");
        }}
      >
        Don't have an account? Register
      </p>
    </div>
  );
};

export default Login;
