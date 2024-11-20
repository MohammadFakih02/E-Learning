import { requestApi } from "../utils/request";
import { useState } from "react";

const AnnouncementStream = () => {
    const [announcements,setAnnouncements] = useState([]);
    
  getAnnouncements = async () => {
    try {
      const result = await requestApi({
        route: `/viewAnnouncementsStream?course_id=${course_id}`,
      });
      setAnnouncements(data);
    } catch(error) {
      console.log(error);
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
