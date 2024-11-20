import React from "react";
import Button from "./Button"; // Assuming you have a Button component

const CourseList = ({ url, courses, onEnroll,onClick }) => {
  return (
    <div>
      <h1>{url === "/viewMyCourses" ? "My Courses" : "All Courses"}</h1>
      <div>
        {courses?.map((course) => (
          <div key={course.course_id} onClick={() => onClick(course.course_id)}>
            <h2>{course.course_name}</h2>
            {onEnroll && (
              <Button
                text="Enroll"
                onClick={() => onEnroll(course.course_id)}
              />
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default CourseList;
