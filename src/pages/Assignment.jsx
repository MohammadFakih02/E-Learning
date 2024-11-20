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
    setFile(e.target.files[0]);
  };

  const handleContentChange = (e) => {
    setContent(e.target.value);
  };

  const handleSubmit = async () => {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("content", content);

    try {
      const result = await requestApi({
        body: formData,
        method: requestMethods.POST,
        route: `/student/submitAssignment.php?ass=${assignment_id}`,
        isMultipart: true, 
      });
      console.log(result);
    } catch (error) {
      console.log(error.response.data.message);
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
