# Gym Management System

The Gym Management System is a comprehensive web application designed to facilitate the management of a fitness center or gym. It offers a range of features for different user roles, including administrators, trainers, and students. This project is built using Next.js, Tailwind CSS, and integrates with various third-party services like Stripe for payments and Cloudinary for image management.

## Features

- **Authentication and Authorization**: Secure login and role-based access control.
- **User-specific Dashboards**: Custom dashboards for admins, trainers, and students.
- **User Profile Management**: Update and manage user profiles, including images and personal information.
- **User Management**: Admins can add, update, and delete users, and assign trainers to students.
- **Attendance Tracking**: Track and manage student attendance.
- **Exercise and Diet Management**: Create, assign, and manage exercise routines and diet plans.
- **Fees Tracking and Payment**: Track fees, send reminders, and process payments via Stripe.
- **Notifications**: Send and manage notifications for users.
- **Responsive Design**: Fully responsive design for optimal viewing on all devices.

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/gym-management-system.git
   ```

2. **Navigate to the project directory**:
   ```bash
   cd gym-management-system
   ```

3. **Install dependencies**:
   ```bash
   npm install
   ```

4. **Set up environment variables**:
   Create a `.env.local` file in the root directory and add the following variables:
   ```
   DATABASE_URL=<your_mongodb_uri>
   NEXTAUTH_SECRET=<next_auth_secret>
   NODE_ENV="development"
   NEXT_PUBLIC_CLOUDINARY_CLOUD_NAME=<your_cloudname>
   NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=<your_stripe_publishable_key>
   NEXT_PUBLIC_STRIPE_SECRET_KEY=<your_stripe_secret_key>
   ```

5. **Run the development server**:
   ```bash
   npm run dev
   ```

6. **Open your browser**:
   Visit [http://localhost:3000](http://localhost:3000) to view the application.

## Usage

### Authentication Pages
- **Sign In**: Users can log in using their email and password. Access is restricted to authorized users.

### Dashboard Pages
- **Admin Dashboard**: View statistics and graphs related to user activity, attendance, and fees.
- **Trainer Dashboard**: View statistics and graphs related to user activity and attendance.
- **Student Dashboard**: View fees status, attendance graph, and activity status.

### User Profile Pages
- **Profile Page**: View and update profile details, including images and personal information.
- **Trainer Page**: View detailed information about trainers.
- **Student Page**: View detailed information about students.

### User Management Pages
- **Add Member Page**: Admins can add trainers and students; trainers can add students.
- **Manage User Page**: Admins can view, update, and delete user profiles, and assign trainers to students.

### Trainers and Students Pages
- **Trainers Page**: View a list of trainers with pagination support.
- **Students Page**: View a list of students with pagination support.

### Attendance Pages
- **Attendance Page**: Create and monitor student attendance.
- **Student Attendance Page**: View and mark daily attendance.

### Exercise and Diet Pages
- **Manage Exercise Page**: Add, view, and delete exercises.
- **Assign Exercise Page**: Assign exercises to students with details like time period and sets.
- **Manage Diet Food Page**: Add, view, and delete foods.
- **Assign Diet Sheet Page**: Assign diet sheets to students with details like time period and meals.
- **Student Exercise Page**: View exercise routines.
- **Student Diet Sheet Page**: View diet sheets.

### Fees Pages
- **Fees Page**: Add, track, and send reminders for student fees.
- **Student Fees Page**: View fees status, pay fees, and see fees history.
- **Student Fees Stripe Checkout Page**: Pay fees using Stripe checkout.
- **Student Payment Success Page**: View payment details after successful payment.

### Notification Page
- **Notification Page**: Send, view, and manage notifications.

## Deployment

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

## Contributing

Contributions to the Gym Management System are welcome! Feel free to fork the repository, create a new branch, and submit pull requests.

## Acknowledgements

- This project was inspired by the need for a comprehensive gym management system.
