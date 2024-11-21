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
    return !isNaN(date.getTime()); // Returns true if valid, false otherwise
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
                deadline 
            },
        });

        console.log("Assignment created:", { course_id, title, content, deadline });
        setAssignment({
            course_id: 0,
            title: "",
            content: "",
            deadline: "",
        });
    } catch (error) {
        console.error("Error creating assignment:", error.response ? error.response.data : error);
    }
};
 

  useEffect(() => {
    getStudents();
  }, []);

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
        <Input
          placeholder={"course id"}
          value={announcement.course_id}
          onChange={(e) => {
            setAnnouncement({
              ...announcement,
              course_id: e.target.value,
            });
          }}
        />
        <Input
          placeholder={"title"}
          value={announcement.title}
          onChange={(e) => {
            setAnnouncement({
              ...announcement,
              title: e.target.value,
            });
          }}
        />
        <Input
          placeholder={"content"}
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

    </>
  );
};

export default Instructor;
