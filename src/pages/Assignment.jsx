import React, { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";
import Input from '../components/Input';
import Button from "../components/Button";

const Assignment = () => {
  const { assignment_id } = useParams();
  const [assignment, setAssignment] = useState({});
  const [file, setFile] = useState(null);
  const [content, setContent] = useState("");
  const [commentContent, setCommentContent] = useState("");
  const [isPrivate, setIsPrivate] = useState(false);
  const [comments, setComments] = useState([]);

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

  const getComments = async () => {
    try {
      const result = await requestApi({
        route: `/viewComments.php?assignment_id=${assignment_id}`,
      });
      setComments(result.data);
    } catch (error) {
      console.log(error.response.data.message);
    }
  };

  useEffect(() => {
    getAssignment();
    getComments();
  }, [assignment_id]);

  const handleFileChange = (e) => {
    const selectedFile = e.target.files[0];
    setFile(selectedFile);
    console.log('Selected file:', selectedFile);
  };

  const handleContentChange = (e) => {
    setContent(e.target.value);
  };

  const handleCommentChange = (e) => {
    setCommentContent(e.target.value);
  };

  const handlePrivateChange = (e) => {
    setIsPrivate(e.target.checked);
  };

  const handleSubmit = async () => {
    const formData = new FormData();

    if (file) {
      console.log('Appending file:', file);
      formData.append("file", file);
    } else {
      console.log('No file selected');
    }

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
      console.log(error.response ? error.response.data.message : error.message);
    }
  };

  const handleCommentSubmit = async () => {
    if (!commentContent) {
      return;
    }

    const data = {
      assignment_id,
      content: commentContent,
      private: isPrivate ? 1 : 0, 
    };

    try {
      const result = await requestApi({
        body: JSON.stringify(data),
        method: requestMethods.POST,
        route: "/student/Comment.php",
      });
      console.log(result);
      setCommentContent("");
      setIsPrivate(false);
      getComments();
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

      <div>
        <h1>Post a Comment</h1>

        <Input
          placeholder={"Write your comment here..."}
          value={commentContent}
          onChange={handleCommentChange}
        />

        <div>
          <label>
            <input
              type="checkbox"
              checked={isPrivate}
              onChange={handlePrivateChange}
            />
            Mark as Private
          </label>
        </div>

        <Button text={"Post Comment"} onClick={handleCommentSubmit} />
      </div>

      <div>
        <h1>Comments</h1>
        <div>
          {comments.length > 0 ? (
            comments.map((comment, index) => (
              <div key={index} className="comment-card">
                <p><strong>{comment.username}</strong> says:</p>
                <p>{comment.content}</p>
                <p><i>{comment.date}</i></p>
                {comment.private && <span style={{ color: 'red' }}>Private</span>}
              </div>
            ))
          ) : (
            <p>No comments yet.</p>
          )}
        </div>
      </div>
    </>
  );
};

export default Assignment;
