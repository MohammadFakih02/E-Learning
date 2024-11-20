import { requestApi } from "../utils/request";
import { useState,useEffect } from "react";
import Button from "./Button";
import { useNavigate } from "react-router-dom";

const AssignmentStream = ({course_id}) => {
    const [assignments,setAssignments] = useState([]);
    const navigate = useNavigate();
  const getAssignments = async () => {
    try {
      const result = await requestApi({
        route: `/viewAssignmentsStream.php?course_id=${course_id}`,
      });
      setAssignments(result.data);
    } catch(error) {
      console.log(error.response.data.message);
    }
  };

  useEffect(() => {
    getAssignments();
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
                <Button text="checkdetails" onClick={()=>{navigate(`/assignment/${assignment.assignment_id}`)}}/>
            </div>
        ))}
    </div>
  );
};
export default AssignmentStream;