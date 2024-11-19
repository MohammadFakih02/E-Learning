import { requestApi } from "../utils/request";
import { requestMethods } from "../utils/enums/requestMethods";
import { useState } from "react";

const assignmentStream = () => {
    const [assignments,setAssignments] = useState([]);
    
  getassignments = async () => {
    try {
      const result = await requestApi({
        route: `/viewAssignmentsStream?course_id=${course_id}`,
      });
      setAssignments(data);
    } catch {}
  };

  useEffect(() => {
    getAssignmnets();
  }, []);

  return (
    <div className="assignments-container">
        {assignments?.map((assignment,index)=>(
            <div className="assignment-card" key={assignment.assignment_id}>
                <div className="assignment-head">
                <h2 className="assignment-title">{assignment.title}</h2>
                <h2>{assignment.created_at}</h2>
                </div>
                <h3>from {assignment.username}</h3>
                <h2>due {assignment.deadline}</h2>
            </div>
        ))}
    </div>
  );
};
