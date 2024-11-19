import React, { useState } from "react";
import Button from "../components/Button";
import Input from "../components/Input";
import { useNavigate } from "react-router-dom";
import { requestApi } from "../../utils/request";

const Login = () => {
  const navigate = useNavigate;
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  return (
    <div>
      <h1>Login</h1>
      <Input
        placeholder={email}
        onChange={(e) => {
          setEmail(e.target.value);
        }}
      />
      <Input
        placeholder={password}
        onChange={(e) => {
          setPassword(e.target.value);
        }}
      />
      <Button
        text={"Login"}
        onClick={async () => {
          try {
            const result = await requestApi({
              body: {
                username,
                password,
              },
              method: POST,
              route: "/login",
            });
            localStorage.setItem("token", result.token);
            const decoded = jwt_decode(result.token);
            const role = decoded.role;

            if (role === "admin") {
              navigate("/admin");
            } else if (role === "instructor") {
              navigate("/instructor");
            } else {
              navigate("/student");
            }
          } catch (error) {
            console.log(error.response.data.message);
          }
        }}
      />
      <h2>Don't have an account?</h2>
      <Button text={"register"} onClick={()=>{navigate("/register")}}/>
    </div>
  );
};
export default Login;
