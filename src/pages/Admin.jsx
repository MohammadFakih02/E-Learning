import React, { useState, useEffect } from "react";
import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";

const Admin = () => {
  const [students, setStudents] = useState([]);
  const [instructors, setInstructors] = useState([]);
  const [courses, setCourses] = useState([]);
  
  const [newInstructor, setNewInstructor] = useState({
    username: "",
    email: "",
    password: "",
    role: "instructor",
  });

  const [newCourse, setNewCourse] = useState({
    course_name: "",
  });

  const [selectedCourses, setSelectedCourses] = useState([]); // To track selected courses

  const getAdminView = async () => {
    try {
      const result = await requestApi({
        route: `/admin/adminView.php`,
      });
      setStudents(result.data.students);
      setInstructors(result.data.instructors);
      setCourses(result.data.courses);
    } catch (error) {
      console.log(error.response?.data?.message || "Error fetching admin view");
    }
  };

  const toggleBanStatus = async (userId, isBanned, category) => {
    try {
      const route = isBanned === "0" ? "/admin/ban.php" : "/admin/unban.php";
      console.log("Sending user_id to:", route, { user_id: userId });
  
      const response = await requestApi({
        route,
        method: requestMethods.POST,
        body: { user_id: userId },
      });
  
      if (category === "student") {
        setStudents((prev) =>
          prev.map((user) =>
            user.user_id === userId
              ? { ...user, is_banned: isBanned === "0" ? "1" : "0" }
              : user
          )
        );
      } else if (category === "instructor") {
        setInstructors((prev) =>
          prev.map((user) =>
            user.user_id === userId
              ? { ...user, is_banned: isBanned === "0" ? "1" : "0" }
              : user
          )
        );
      }
  
      console.log(response.message);
    } catch (error) {
      console.error("Error:", error.response?.data?.message || error.message);
    }
  };

  useEffect(() => {
    getAdminView();
  }, []);

  const handleAssignCourses = async (instructorId, courseIds) => {
    if (courseIds.length === 0) {
      return;
    }

    try {
      const response = await requestApi({
        route: `/admin/assignInstructor.php?user_id=${instructorId}&course_ids=${courseIds.join(",")}`,
      });
      console.log(response);
    } catch (error) {
      console.error("Error:", error.response?.data?.message || error.message);
    }
  };

  const handleDeleteCourse = async (courseId) => {
    try {
      const body = {
        course_id: courseId,
      };

      const response = await requestApi({
        route: "/admin/deleteCourse.php",
        method: requestMethods.POST,
        body: body,
      });

      if (response.message) {
        setCourses((prevCourses) => 
          prevCourses.filter((course) => course.course_id !== courseId)
        );
        console.log(response.message);
      }
    } catch (error) {
      console.error("Error:", error.response?.data?.message || error.message);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setNewInstructor((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleCourseInputChange = (e) => {
    const { name, value } = e.target;
    setNewCourse((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleAddInstructor = async (e) => {
    e.preventDefault(); // Ensure we prevent default form submission

    console.log("Creating instructor with data:", newInstructor, selectedCourses); // Log the data being submitted

    try {
      const response = await requestApi({
        route: "/admin/createInstructor.php",
        method: requestMethods.POST,
        body: {
          ...newInstructor,
          course_ids: selectedCourses, // Pass selected courses here
        },
      });

      console.log("Instructor created successfully:", response); // Log the response from API

      if (response.message) {
        // Check if response.data contains user_id
        if (response.data && response.data.user_id) {
          setInstructors((prev) => [
            ...prev,
            {
              user_id: response.data.user_id,  // Use the user_id from the response
              username: newInstructor.username,
              role: newInstructor.role,
              is_banned: "0",
            },
          ]);
          console.log(response.message);
          setNewInstructor({
            username: "",
            email: "",
            password: "",
            role: "instructor",
          });
          setSelectedCourses([]); // Reset the selected courses
        } else {
          console.error('User ID is missing in the response');
        }
      }
    } catch (error) {
      console.error("Error:", error.response?.data?.message || error.message);
    }
};


  const handleAddCourse = async () => {
    try {
      const response = await requestApi({
        route: "/admin/createCourse.php",
        method: requestMethods.POST,
        body: newCourse,
      });
  
      if (response.message) {
        if (response.data && response.data.course_id) {
          setCourses((prev) => [
            ...prev,
            {
              course_id: response.data.course_id,
              course_name: newCourse.course_name,
            },
          ]);
          console.log(response.message);
          setNewCourse({
            course_name: "",
          });
        } else {
          console.error('Invalid response data:', response);
        }
      }
    } catch (error) {
      console.error("Error:", error.response?.data?.message || error.message);
    }
  };

  return (
    <div>
      <h2>Students</h2>
      <table border="1">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Banned</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {students.map((student) => (
            <tr key={student.user_id}>
              <td>{student.user_id}</td>
              <td>{student.username}</td>
              <td>{student.role}</td>
              <td>{student.is_banned === "1" ? "Yes" : "No"}</td>
              <td>
                <button
                  onClick={() =>
                    toggleBanStatus(student.user_id, student.is_banned, "student")
                  }
                >
                  {student.is_banned === "1" ? "Unban" : "Ban"}
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <h2>Instructors</h2>
      <table border="1">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Banned</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {instructors.map((instructor) => (
            <tr key={instructor.user_id}>
              <td>{instructor.user_id}</td>
              <td>{instructor.username}</td>
              <td>{instructor.role}</td>
              <td>{instructor.is_banned === "1" ? "Yes" : "No"}</td>
              <td>
                <button
                  onClick={() =>
                    toggleBanStatus(instructor.user_id, instructor.is_banned, "instructor")
                  }
                >
                  {instructor.is_banned === "1" ? "Unban" : "Ban"}
                </button>

                <select
                  multiple
                  onChange={(e) => handleAssignCourses(instructor.user_id, Array.from(e.target.selectedOptions, option => option.value))}
                  defaultValue={[]}
                  style={{ minWidth: '200px', height: '150px' }}
                >
                  <option value="" disabled>
                    Assign Courses
                  </option>
                  {courses.map((course) => (
                    <option key={course.course_id} value={course.course_id}>
                      {course.course_name}
                    </option>
                  ))}
                </select>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Add Instructor Form */}
      <h2>Add Instructor</h2>
      <form onSubmit={handleAddInstructor}>
        <div>
          <label>Username</label>
          <input
            type="text"
            name="username"
            value={newInstructor.username}
            onChange={handleInputChange}
            required
          />
        </div>
        <div>
          <label>Email</label>
          <input
            type="email"
            name="email"
            value={newInstructor.email}
            onChange={handleInputChange}
            required
          />
        </div>
        <div>
          <label>Password</label>
          <input
            type="password"
            name="password"
            value={newInstructor.password}
            onChange={handleInputChange}
            required
          />
        </div>
        
        {/* Add Course Selection */}
        <div>
          <label>Assign Courses</label>
          <select
            multiple
            value={selectedCourses}
            onChange={(e) => setSelectedCourses(Array.from(e.target.selectedOptions, option => option.value))}
            style={{ minWidth: '200px', height: '150px' }}
          >
            {courses.map((course) => (
              <option key={course.course_id} value={course.course_id}>
                {course.course_name}
              </option>
            ))}
          </select>
        </div>
        
        <button type="submit">Create Instructor</button>
      </form>

      {/* Add Course Form */}
      <h2>Add Course</h2>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleAddCourse();
        }}
      >
        <div>
          <label>Course Name</label>
          <input
            type="text"
            name="course_name"
            value={newCourse.course_name}
            onChange={handleCourseInputChange}
            required
          />
        </div>
        <button type="submit">Create Course</button>
      </form>

      <h2>Courses</h2>
      <table border="1">
        <thead>
          <tr>
            <th>ID</th>
            <th>Course Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {courses.map((course) => (
            <tr key={course.course_id}>
              <td>{course.course_id}</td>
              <td>{course.course_name}</td>
              <td>
                <button onClick={() => handleDeleteCourse(course.course_id)}>Delete</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default Admin;
