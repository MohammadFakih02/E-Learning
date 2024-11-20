const CourseCard = (coursename, { button }) => {
  return (
    <div className="course-card">
      <h2>{coursename}</h2>
      {button}
    </div>
  );
};