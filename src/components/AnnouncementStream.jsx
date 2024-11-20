import { requestApi } from "../utils/request";
import { useState,useEffect } from "react";

const AnnouncementStream = ({course_id}) => {
    const [announcements,setAnnouncements] = useState([]);
    
  const getAnnouncements = async () => {
    try {
      const result = await requestApi({
        route: `/viewAnnouncementsStream.php?course_id=${course_id}`,
      });
      setAnnouncements(result.data);
    } catch(error) {
      console.log(error.response.data.message);
    }
  };

  useEffect(() => {
    getAnnouncements();
  }, []);

  return (
    <div className="announcements-container">
        {announcements?.map((announcement,index)=>(
            <div className="announcement-card">
                <div className="announcement-head">
                <h2 className="announcement-title">{announcement.title}</h2>
                <h2>{announcement.date}</h2>
                </div>
                <h3>by {announcement.username}</h3>
                <p>{announcement.content}</p>
            </div>
        ))}
    </div>
  );
};
export default AnnouncementStream;
