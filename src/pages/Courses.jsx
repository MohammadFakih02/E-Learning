import { useState, useEffect } from "react";
import { requestApi } from "../utils/request";
import CourseList from "../components/CourseList";
import { useNavigate } from "react-router-dom";
const Courses = () => {
  const navigate = useNavigate();
  const [myCourses, setMyCourses] = useState([]);
  const [allCourses, setAllCourses] = useState([]);

  const fetchCourses = async () => {
    try {
      const myCoursesResult = await requestApi({ route: "/viewMyCourses.php" });
      setMyCourses(myCoursesResult.data);

      const allCoursesResult = await requestApi({ route: "/viewCourses.php" });
      setAllCourses(allCoursesResult.data);
    } catch (error) {
      console.log(error.response?.data?.message || "Error fetching courses");
    }
  };

  const handleEnroll = async (courseId) => {
    try {
      const result = await requestApi({
        route: `/student/enroll.php?course_id=${courseId}`,
      });

      const enrolledCourse = allCourses.find((course) => course.course_id === courseId);
      if (enrolledCourse) {
        setMyCourses((prev) => [...prev, enrolledCourse]);
        setAllCourses((prev) => prev.filter((course) => course.course_id !== courseId));
      }

      console.log(`Enrollment successful for course ID: ${courseId}`, result);
    } catch (error) {
      console.log(error.response?.data?.message || "An error occurred");
    }
  };

  useEffect(() => {
    fetchCourses();
  }, []);

  const handleCourseClick = (courseId) => {
    navigate(`/streams/${courseId}`);
  };

  return (
    <div>
      <CourseList url="/viewMyCourses" courses={myCourses} onEnroll={null} onClick={handleCourseClick}/>
      <CourseList url="/viewCourses" courses={allCourses} onEnroll={handleEnroll} />
    </div>
  );
};

export default Courses;
