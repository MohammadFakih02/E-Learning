import axios from "axios";

axios.defaults.baseURL = "http://localhost/backend";
// axios.defaults.headers.Authorization = localStorage.token

export const requestApi = async ({ route, method = "GET", body }) => {
  try {
    const response = await axios.request({
      url: `${route}.php`,
      method,
      data: body,
      headers: {
        "Content-Type": "application/json",
        Authorization: localStorage.token,
      },
    });

    return response.data;
  } catch (error) {
    console.log("======== Error =========");
    console.log(error.response.data.message);
    console.log("======== // =========");

    throw error;
  }
};
