import axios from "axios";
axios.defaults.baseURL = "http://localhost/elearning/backend";
// axios.defaults.headers.Authorization = localStorage.token

export const requestApi = async ({ route, method = "GET", body }) => {
  try {
    const response = await axios.request({
      url: `${route}`,
      method,
      data: body,
      headers: {
        "Content-Type": "application/json",
        Authorization: localStorage.token,
      },
    });

    return response.data;
  } catch (error) {
    throw error;
  }
};