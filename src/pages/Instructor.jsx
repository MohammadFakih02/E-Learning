import React, { useState, useEffect } from "react";
import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";
import Button from "../components/Button";
import Input from "../components/Input";

const Instructor = () => {
  const [announcement, setAnnouncement] = useState({
    course_id: 0,
    title: "",
    content: "",
  });
  const [assignment, setAssignment] = useState({
    course_id: 0,
    title: "",
    content: "",
    deadline: "",
  });

  const [students, setStudents] = useState([]);
  const [submissions, setSubmissions] = useState([]);
  const [assignmentId, setAssignmentId] = useState(""); // Store assignmentId as string

  // Fetch students from the API
  const getStudents = async () => {
    try {
      const result = await requestApi({
        route: "/instructor/getStudents.php",
      });
      setStudents(result.data);
    } catch (error) {
      console.log(error.response ? error.response.data.message : "An error occurred");
    }
  };

  const getSubmissions = async (id) => {
    try {
      const result = await requestApi({
        route: `/instructor/viewSubmissions.php?assignment_id=${id}`,
      });

      if (result.status === "success") {
        setSubmissions(result.submissions);
      } else {
        console.error("Failed to fetch submissions");
      }
    } catch (error) {
      console.log(error.response ? error.response.data.message : "An error occurred");
    }
  };

  const createAnnouncement = async () => {
    const { course_id, title, content } = announcement;
    try {
      const result = await requestApi({
        route: "/instructor/createAnnouncement.php",
        method: requestMethods.POST,
        body: { course_id: parseInt(course_id), title, content },
      });
      console.log({ course_id: parseInt(course_id), title, content });
      setAnnouncement({
        course_id: 0,
        title: "",
        content: "",
      });
    } catch (error) {
      console.log(error.response ? error.response.data.message : "An error occurred");
    }
  };

  const isValidDate = (dateString) => {
    const date = new Date(dateString);
    return !isNaN(date.getTime()); 
  };

  const createAssignment = async () => {
    const { course_id, title, content, deadline } = assignment;

    if (!isValidDate(deadline)) {
      console.error("Invalid deadline date");
      return;
    }

    try {
      const result = await requestApi({
        route: "/instructor/createAssignment.php",
        method: requestMethods.POST,
        body: {
          course_id: parseInt(course_id),
          title,
          content,
          deadline,
        },
      });

      console.log("Assignment created:", { course_id, title, content, deadline });
      setAssignment({
        course_id: 0,
        title: "",
        content: "",
        deadline: "",
      });

      getSubmissions(result.assignment_id);
    } catch (error) {
      console.error("Error creating assignment:", error.response ? error.response.data : error);
    }
  };

  useEffect(() => {
    getStudents();
  }, []);


  useEffect(() => {
    if (assignmentId) {
      getSubmissions(assignmentId);
    }
  }, [assignmentId]);

  return (
    <>
      <div>
        <h2>Students</h2>
        <table border="1">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
            </tr>
          </thead>
          <tbody>
            {students.length > 0 ? (
              students.map((student) => (
                <tr key={student.user_id}>
                  <td>{student.user_id}</td>
                  <td>{student.username}</td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="2">No students found</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <div>
        <h2>Create Announcement</h2>
        <Input
          placeholder={"Course ID"}
          value={announcement.course_id}
          onChange={(e) => {
            setAnnouncement({
              ...announcement,
              course_id: e.target.value,
            });
          }}
        />
        <Input
          placeholder={"Title"}
          value={announcement.title}
          onChange={(e) => {
            setAnnouncement({
              ...announcement,
              title: e.target.value,
            });
          }}
        />
        <Input
          placeholder={"Content"}
          value={announcement.content}
          onChange={(e) => {
            setAnnouncement({
              ...announcement,
              content: e.target.value,
            });
          }}
        />
        <Button text={"Create Announcement"} onClick={createAnnouncement} />
      </div>

      <div>
        <h2>Create Assignment</h2>
        <Input
          placeholder={"Course ID"}
          value={assignment.course_id}
          onChange={(e) => setAssignment({ ...assignment, course_id: e.target.value })}
        />
        <Input
          placeholder={"Title"}
          value={assignment.title}
          onChange={(e) => setAssignment({ ...assignment, title: e.target.value })}
        />
        <Input
          placeholder={"Content"}
          value={assignment.content}
          onChange={(e) => setAssignment({ ...assignment, content: e.target.value })}
        />
        <input
          type="date"
          placeholder="Deadline"
          value={assignment.deadline}
          onChange={(e) => setAssignment({ ...assignment, deadline: e.target.value })}
        />
        <Button text={"Create Assignment"} onClick={createAssignment} />
      </div>

      <div>
        <h2>View Submissions for Assignment</h2>
        <Input
          placeholder={"Enter Assignment ID"}
          value={assignmentId}
          onChange={(e) => setAssignmentId(e.target.value)}
        />
      </div>

      {assignmentId && (
        <div>
          <h2>Submissions for Assignment {assignmentId}</h2>
          <table border="1">
            <thead>
              <tr>
                <th>Student Name</th>
                <th>File Name</th>
                <th>Submission Date</th>
                <th>Content</th>
              </tr>
            </thead>
            <tbody>
              {submissions.length > 0 ? (
                submissions.map((submission) => (
                  <tr key={submission.submission_id}>
                    <td>{submission.student_name}</td>
                    <td>
                      <a
                        href={submission.file_path}
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                        {submission.file_name}
                      </a>
                    </td>
                    <td>{new Date(submission.created_at).toLocaleString()}</td>
                    <td>{submission.content}</td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="4">No submissions yet</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}
    </>
  );
};

export default Instructor;
