import React from "react";
import { useParams } from "react-router-dom";
import { useState } from "react";
import AssignmentStream from "../components/AssignmentStream";
import AnnouncementStream from "../components/AnnouncementStream";
const Streams = () => {
  const { courseId } = useParams();
  const [showAssignments, setShowAssignments] = useState(true);

  const toggleStream = () => {
    setShowAssignments(!showAssignments);
  };

  return (
    <div>
      <div className="toggle-buttons">
        <button onClick={toggleStream}>
          {showAssignments ? "Show Announcements" : "Show Assignments"}
        </button>
      </div>

      {showAssignments ? (
        <AssignmentStream course_id={courseId} />
      ) : (
        <AnnouncementStream course_id={courseId} />
      )}
    </div>
  );
};

export default Streams;
