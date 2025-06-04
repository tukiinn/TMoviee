import AdminMembershipPackages from './Pages/Admin/MembershipPackages';
import MembershipPackages from './Pages/Membership/MembershipPackages';

const routes = [
    {
        path: '/membership',
        element: <MembershipPackages />,
        auth: true
    },
    {
        path: '/admin/membership-packages',
        element: <AdminMembershipPackages />,
        auth: true,
        admin: true
    }
];

export default routes; 