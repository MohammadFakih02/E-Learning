import React, { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";
import Input from '../components/Input';
import Button from "../components/Button";
const Assignment = () => {
  const { assignment_id } = useParams();
  const [assignment, setAssignment] = useState({});

  const getAssignment = async () => {
    try {
      const result = await requestApi({
        route: `/assignmentDetails.php?assignment_id=${assignment_id}`,
      });
      setAssignment(result.data);
    } catch (error) {
      console.log(error.response.data.message);
    }
  };

  useEffect(() => {
    getAssignment();
  }, [assignment_id]);

  const [file, setFile] = useState(null);
  const [content, setContent] = useState("");

  const handleFileChange = (e) => {
    const selectedFile = e.target.files[0];
    setFile(selectedFile); // Set the selected file in state
    console.log('Selected file:', selectedFile); // Debugging log to check the file selected
  };
  

  const handleContentChange = (e) => {
    setContent(e.target.value);
  };

  const handleSubmit = async () => {
    const formData = new FormData();
  
    // Check if the file is not null or undefined
    if (file) {
      console.log('Appending file:', file); // Debugging log
      formData.append("file", file);
    } else {
      console.log('No file selected');
    }
  
    formData.append("content", content); // Append content as well
  
    try {
      const result = await requestApi({
        body: formData,
        method: requestMethods.POST,
        route: `/student/submitAssignment.php?ass=${assignment_id}`,
        isMultipart: true, // Ensure this flag is set for multipart/form-data
      });
      console.log(result); // Log the response
    } catch (error) {
      console.log(error.response ? error.response.data.message : error.message);
    }
  };
  
  

  return (
    <>
      <div className="assignment-card" key={assignment.assignment_id}>
        <div className="assignment-head">
          <h2 className="assignment-title">{assignment.title}</h2>
          <h2>{assignment.created_at}</h2>
        </div>
        <h3>from {assignment.username}</h3>
        <p>{assignment.description}</p>
        <h2>due {assignment.deadline}</h2>
      </div>

      <div>
        <h1>File Upload</h1>

        <Input
          placeholder={"Content (optional)"}
          value={content}
          onChange={handleContentChange}
        />

        <input
          type="file"
          onChange={handleFileChange}
          accept="*/*"
          required
        />

        <Button text={"Upload File"} onClick={handleSubmit} />
      </div>
    </>
  );
};

export default Assignment;
